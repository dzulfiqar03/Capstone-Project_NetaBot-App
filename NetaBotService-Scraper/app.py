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

def scrape_prodnetafarm():
    scrape_status["running"] = True
    scrape_status["total_scraped"] = 0
    scrape_status["last_product"] = ""
    scrape_status["message"] = "Mulai scraping…"

    API_ENDPOINT = os.environ.get('API_ENDPOINT')
    BASE_URL = "https://www.tokopedia.com/netafarm/product?page={}"
    current_page = 1
    all_data = []
    seen_links = set()  # untuk track link yang sudah di-scrape

    # Setup Selenium headless
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
            img = driver.find_element(By.CSS_SELECTOR, "img[src]").get_attribute("src")
            return img
        except:
            return "-"

    try:
        while True:
            scrape_status["message"] = f"Scraping halaman {current_page}..."
            driver.get(BASE_URL.format(current_page))
            time.sleep(3)

            # Scroll hingga semua produk termuat
            last_height = driver.execute_script("return document.body.scrollHeight")
            while True:
                driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
                time.sleep(2)
                new_height = driver.execute_script("return document.body.scrollHeight")
                if new_height == last_height:
                    break
                last_height = new_height

            # Ambil semua link produk yang mengandung /product/
            produk_elements = driver.find_elements(By.CSS_SELECTOR, "a[href*='/product/']")
            links = [p.get_attribute("href") for p in produk_elements if p.get_attribute("href") not in seen_links]

            if not links:
                scrape_status["message"] = "Tidak ada link baru, selesai scraping."
                break

            for link in links:
                try:
                    driver.get(link)
                    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "h1")))
                    time.sleep(1)

                    soup = BeautifulSoup(driver.page_source, "html.parser")

                    # Nama
                    nama_elem = soup.find("h1")
                    nama = nama_elem.text.strip() if nama_elem else "-"

                    # Harga
                    harga_elem = soup.find("div", {"data-testid": "lblPDPDetailProductPrice"})
                    harga_text = harga_elem.text.strip() if harga_elem else "0"
                    harga_num = int(re.sub(r'[^0-9]', '', harga_text)) if harga_text else 0

                    # Rating
                    rating_elem = soup.find("span", {"data-testid": "lblPDPDetailProductRatingNumber"})
                    rating = rating_elem.text.strip() if rating_elem else "0"

                    # Sold
                    sold_elem = soup.find("p", {"data-testid": "lblPDPDetailProductSoldCounter"})
                    sold = re.search(r'\d+', sold_elem.text).group(0) if sold_elem and re.search(r'\d+', sold_elem.text) else "0"

                    # Deskripsi
                    desc_elem = driver.find_elements(By.CSS_SELECTOR, "div[data-testid*='DescriptionProduk']")
                    deskripsi = BeautifulSoup(desc_elem[0].get_attribute("innerHTML"), "html.parser").get_text("\n").strip() if desc_elem else "Tidak ada deskripsi"

                    # Gambar
                    gambar = get_product_image()

                    # Simpan ke API
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

                    # Simpan lokal
                    all_data.append({
                        "No": scrape_status["total_scraped"] + 1,
                        "Nama": nama,
                        "Harga": harga_text,
                        "Rating": rating,
                        "Sold": sold,
                        "Deskripsi": deskripsi,
                        "Gambar": gambar,
                        "Link": link
                    })

                    scrape_status["total_scraped"] += 1
                    scrape_status["last_product"] = nama
                    scrape_status["message"] = f"Produk ke-{scrape_status['total_scraped']}: {nama}"

                    seen_links.add(link)

                    # Delay agar tidak terbanned
                    time.sleep(1 + (0.5 * (scrape_status["total_scraped"] % 3)))  # random-ish delay

                except Exception as e:
                    scrape_status["message"] = f"Error detail: {e}"
                    continue

            current_page += 1

    finally:
        driver.quit()
        df = pd.DataFrame(all_data)
        df.to_excel("produk_netafarm.xlsx", index=False)
        scrape_status["message"] = f"Selesai! Total produk tersimpan: {len(df)}"
        scrape_status["running"] = False

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
