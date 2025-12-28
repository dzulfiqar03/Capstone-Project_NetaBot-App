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

# Global status untuk monitoring
scrape_status = {
    "running": False,
    "total_scraped": 0,
    "last_product": "",
    "message": ""
}

def scrape_prodnetafarm(MAX_SCRAPE=200):
    scrape_status["running"] = True
    scrape_status["total_scraped"] = 0
    scrape_status["last_product"] = ""
    scrape_status["message"] = "Mulai scraping…"

    API_ENDPOINT = os.environ.get('API_ENDPOINT')
    BASE_URL = "https://www.tokopedia.com/netafarm/product?page={}"
    current_page = 1
    all_data = []
    total_scraped = 0

    # Setup Selenium headless Chromium
    options = Options()
    options.binary_location = "/usr/bin/chromium"
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    options.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    )

    service = Service("/usr/bin/chromedriver")
    driver = webdriver.Chrome(service=service, options=options)
    wait = WebDriverWait(driver, 10)

    def get_product_image():
        try:
            return driver.find_element(By.CSS_SELECTOR, "img[data-testid='PDPMainImage']").get_attribute("src")
        except:
            return "-"

    try:
        while total_scraped < MAX_SCRAPE:
            scrape_status["message"] = f"Scraping halaman {current_page}..."
            driver.get(BASE_URL.format(current_page))
            time.sleep(3)

            # Scroll hingga semua item termuat
            last_height = driver.execute_script("return document.body.scrollHeight")
            while True:
                driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
                time.sleep(2)
                new_height = driver.execute_script("return document.body.scrollHeight")
                if new_height == last_height:
                    break
                last_height = new_height

            # Ambil semua link produk yang mengandung /product/
            produk_elements = driver.find_elements(By.CSS_SELECTOR, "div.css-tjjb18 div.css-79elbk > a")
            scrape_status["message"] = f"Ditemukan {len(produk_elements)} link produk"
            links = [p.get_attribute("href") for p in produk_elements if p.get_attribute("href")]

            if not links:
                scrape_status["message"] = "Tidak ada link produk, hentikan scraping."
                break

            # Loop per produk
            for link in links:
                if total_scraped >= MAX_SCRAPE:
                    break

                try:
                    driver.get(link)
                    wait.until(EC.presence_of_element_located(
                        (By.CSS_SELECTOR, "h1[data-testid='lblPDPDetailProductName']")
                    ))
                    time.sleep(1)

                    soup = BeautifulSoup(driver.page_source, "html.parser")

                    # Ambil rating & sold
                    json_script = soup.find("script", {"id": "pdp-script"})
                    rating = "0"
                    sold = "0"
                    if json_script:
                        try:
                            data_json = json.loads(json_script.text)
                            if "rating" in data_json and "score" in data_json["rating"]:
                                rating = str(data_json["rating"]["score"])
                            if "txStats" in data_json and "countSold" in data_json["txStats"]:
                                sold = str(data_json["txStats"]["countSold"])
                        except:
                            pass

                    if sold == "0":
                        sold_elem = soup.find("p", {"data-testid": "lblPDPDetailProductSoldCounter"})
                        if sold_elem:
                            angka = re.findall(r'\d+', sold_elem.text)
                            if angka:
                                sold = angka[0]

                    if rating == "0":
                        rating_elem = soup.find("span", {"data-testid": "lblPDPDetailProductRatingNumber"})
                        if rating_elem:
                            rating = rating_elem.text.strip()

                    # Nama & Harga
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

                    all_data.append({
                        "No": total_scraped + 1,
                        "Nama": nama,
                        "Harga": harga_text,
                        "Rating": rating,
                        "Sold": sold,
                        "Deskripsi": deskripsi,
                        "Gambar": gambar,
                        "Link": link
                    })

                    total_scraped += 1
                    scrape_status["total_scraped"] = total_scraped
                    scrape_status["last_product"] = nama
                    scrape_status["message"] = f"Produk ke-{total_scraped}: {nama}"

                except Exception as e:
                    scrape_status["message"] = f"Error detail: {e}"
                    continue

            if total_scraped >= MAX_SCRAPE:
                break

            current_page += 1

    finally:
        driver.quit()
        df = pd.DataFrame(all_data)
        df.to_excel("produk_netafarm.xlsx", index=False)
        scrape_status["message"] = f"Selesai! Total produk tersimpan: {len(df)}"
        scrape_status["running"] = False

# Route untuk memulai scraping
@app.route("/scrape", methods=["GET"])
def scrape_route():
    if scrape_status["running"]:
        return jsonify({"status":"running","message":"Scraping sedang berjalan"})
    thread = Thread(target=scrape_prodnetafarm, daemon=True)
    thread.start()
    return jsonify({"status":"started","message":"Scraping dimulai di background"})

# Route untuk cek status
@app.route("/scrape_status", methods=["GET"])
def status_route():
    return jsonify(scrape_status)

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    app.run(host="0.0.0.0", port=port, debug=False)
