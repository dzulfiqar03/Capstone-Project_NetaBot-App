from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
from flask import Flask, jsonify
import pandas as pd
import time
import requests
import re
import json
import os
from threading import Thread

app = Flask(__name__)

scrape_status = {
    "running": False,
    "total_scraped": 0,
    "last_product": "",
    "message": ""
}

def scrape_prodnetafarm(MAX_SCRAPE=1000):
    scrape_status.update({"running": True, "total_scraped": 0, "last_product": "", "message": "Mulai scraping…"})
    
    API_ENDPOINT = os.environ.get('API_ENDPOINT')
    BASE_URL = "https://www.tokopedia.com/netafarm/product?page={}"
    current_page = 1
    all_data = []
    total_scraped = 0

    # Selenium headless Chromium
    options = Options()
    options.binary_location = "/usr/bin/chromium"
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    options.add_argument("user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36")

    service = Service("/usr/bin/chromedriver")
    driver = webdriver.Chrome(service=service, options=options)
    wait = WebDriverWait(driver, 10)

    def get_product_image():
        try:
            img = driver.find_element(
                By.CSS_SELECTOR,
                "div#pdp_comp-product_media img[src]"
            ).get_attribute("src")
            return img
        except:
            pass

        try:
            bg = driver.find_element(
                By.CSS_SELECTOR,
                "div#pdp_comp-product_media div.magnifier"
            ).value_of_css_property("background-image")

            url = re.search(r'url\(\"(.*)\"\)', bg)
            if url:
                return url.group(1)
        except:
            pass

        try:
            img = driver.find_element(
                By.CSS_SELECTOR,
                "img[data-testid='PDPMainImage']"
            ).get_attribute("src")
            return img
        except:
            return "-"

    try:
        while total_scraped < MAX_SCRAPE:
            scrape_status["message"] = f"Scraping halaman {current_page}…"
            driver.get(BASE_URL.format(current_page))
            time.sleep(2)  # tunggu halaman load

            # Tunggu minimal 1 produk muncul
            try:
                wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "a.css-79elbk")))
            except:
                scrape_status["message"] = f"Tidak ada produk di halaman {current_page}, hentikan scraping."
                break

            # Ambil semua link produk di halaman ini
            produk_elements = driver.find_elements(By.CSS_SELECTOR, "a.css-79elbk")
            links = list({p.get_attribute("href") for p in produk_elements if p.get_attribute("href")})
            scrape_status["message"] = f"Ditemukan {len(links)} produk di page {current_page}"

            if not links:
                break

            for link in links:
                if total_scraped >= MAX_SCRAPE:
                    break

                try:
                    driver.get(link)
                    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "h1[data-testid='lblPDPDetailProductName']")))
                    time.sleep(1)

                    soup = BeautifulSoup(driver.page_source, "html.parser")
                    # Ambil JSON Tokopedia
                    json_script = soup.find("script", {"id": "pdp-script"})
                    rating, sold = "0", "0"
                    if json_script:
                        try:
                            data_json = json.loads(json_script.text)
                            rating = str(data_json.get("rating", {}).get("score", "0"))
                            sold = str(data_json.get("txStats", {}).get("countSold", "0"))
                        except:
                            pass

                    if sold == "0":
                        sold_elem = soup.find("p", {"data-testid": "lblPDPDetailProductSoldCounter"})
                        if sold_elem:
                            angka = re.findall(r'\d+', sold_elem.text)
                            if angka: sold = angka[0]

                    if rating == "0":
                        rating_elem = soup.find("span", {"data-testid": "lblPDPDetailProductRatingNumber"})
                        if rating_elem: rating = rating_elem.text.strip()

                    nama = soup.find("h1", {"data-testid": "lblPDPDetailProductName"}).text.strip()
                    harga_text = soup.find("div", {"data-testid": "lblPDPDetailProductPrice"}).text.strip()
                    harga_num = int(re.sub(r'[^0-9]', '', harga_text))
                    gambar = get_product_image()

                    try:
                        desc_box = driver.find_element(By.CSS_SELECTOR, "div[data-testid='lblPDPDescriptionProduk']")
                        deskripsi = BeautifulSoup(desc_box.get_attribute("innerHTML"), "html.parser").get_text("\n").strip()
                    except:
                        deskripsi = "Tidak ada deskripsi"

                    # Simpan ke API jika ada
                    if API_ENDPOINT:
                        try:
                            requests.post(API_ENDPOINT, json={
                                "name": nama,
                                "price": harga_num,
                                "description": deskripsi,
                                "url_images": gambar,
                                "rating": rating,
                                "sold": sold,
                                "link": link
                            })
                        except:
                            pass

                    total_scraped += 1
                    scrape_status.update({
                        "total_scraped": total_scraped,
                        "last_product": nama,
                        "message": f"Produk ke-{total_scraped}: {nama}"
                    })

                    all_data.append({
                        "No": total_scraped,
                        "Nama": nama,
                        "Harga": harga_text,
                        "Rating": rating,
                        "Sold": sold,
                        "Deskripsi": deskripsi,
                        "Gambar": gambar,
                        "Link": link
                    })

                except Exception as e:
                    scrape_status["message"] = f"Error detail: {e}"
                    continue

            current_page += 1  # lanjut ke halaman berikutnya

    finally:
        driver.quit()
        df = pd.DataFrame(all_data)
        df.to_excel("produk_netafarm.xlsx", index=False)
        scrape_status.update({
            "message": f"Selesai! Total produk tersimpan: {len(df)}",
            "running": False
        })

@app.route("/scrape", methods=["GET"])
def scrape_route():
    if scrape_status["running"]:
        return jsonify({"status":"running","message":"Scraping sedang berjalan"})
    thread = Thread(target=scrape_prodnetafarm, daemon=True)
    thread.start()
    return jsonify({"status":"started","message":"Scraping dimulai di background"})

@app.route("/scrape_status", methods=["GET"])
def status_route():
    return jsonify(scrape_status)

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    app.run(host="0.0.0.0", port=port, debug=False)
