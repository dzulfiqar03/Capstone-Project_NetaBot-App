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
import os  # Tambahkan untuk environment variables
from webdriver_manager.chrome import ChromeDriverManager  # Tambahkan ini

app = Flask(__name__)

def scrape_prodnetafarm():
    API_ENDPOINT = os.environ.get('API_ENDPOINT')  # Gunakan env var
    BASE_URL = "https://www.tokopedia.com/netafarm/product?page={}"

    options = Options()
    options.binary_location = "/usr/bin/chromium"
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")

    service = Service("/usr/bin/chromedriver")
    driver = webdriver.Chrome(service=service, options=options)
    wait = WebDriverWait(driver, 20)

    current_page = 1
    total_scraped = 0
    all_data = []

    print("\n🚀 Mulai scraping NETAFARM...\n")

    def get_product_image():
        try:
            img = driver.find_element(By.CSS_SELECTOR, "img[data-testid='PDPMainImage']").get_attribute("src")
            return img
        except:
            return "-"

    while True:
        print(f"=== Halaman {current_page} ===")
        driver.get(BASE_URL.format(current_page))
        time.sleep(3)

        # Scroll ke bawah untuk memuat semua produk
        last_height = driver.execute_script("return document.body.scrollHeight")
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1)
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height

        # Ambil semua link produk dengan selector stabil
        produk_elements = driver.find_elements(By.CSS_SELECTOR, "a[href^='https://www.tokopedia.com/netafarm/']")
        produk_links = list(set([p.get_attribute("href") for p in produk_elements]))
        print(f"📦 {len(produk_links)} produk ditemukan")

        if len(produk_links) == 0:
            print("🚫 Tidak ada produk lagi. Scraping selesai!")
            break

        for link in produk_links:
            try:
                driver.get(link)
                wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "h1[data-testid='lblPDPDetailProductName']")))
                time.sleep(1)

                soup = BeautifulSoup(driver.page_source, "html.parser")

                # Ambil JSON Tokopedia
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

                # Fallback selector
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

                # Nama
                nama_elem = soup.find("h1", {"data-testid": "lblPDPDetailProductName"})
                nama = nama_elem.text.strip() if nama_elem else "Unknown"

                # Harga
                harga_elem = soup.find("div", {"data-testid": "lblPDPDetailProductPrice"})
                harga_text = harga_elem.text.strip() if harga_elem else "0"
                harga_num = int(re.sub(r'[^0-9]', '', harga_text))

                # Gambar
                gambar = get_product_image()

                # Deskripsi
                try:
                    desc_box = driver.find_element(By.CSS_SELECTOR, "div[data-testid='lblPDPDescriptionProduk']")
                    deskripsi = BeautifulSoup(desc_box.get_attribute("innerHTML"), "html.parser").get_text(separator="\n").strip()
                except:
                    deskripsi = "Tidak ada deskripsi"

                # Kirim ke API
                data = {
                    "name": nama,
                    "price": harga_num,
                    "description": deskripsi,
                    "url_images": gambar,
                    "rating": rating,
                    "sold": sold,
                    "link": link
                }
                try:
                    req = requests.post(API_ENDPOINT, json=data)
                    status = "✅" if req.status_code == 200 else f"⚠️({req.status_code})"
                except:
                    status = "⚠️(API Error)"

                total_scraped += 1
                print(f"{status} {total_scraped}. {nama} | ⭐ {rating} | 🟢 {sold}")

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
                print(f"❌ Error: {e}")
                continue

        current_page += 1

    driver.quit()
    df = pd.DataFrame(all_data)
    df.to_excel("produk_netafarm.xlsx", index=False)
    print(f"\n🎉 Selesai! Total produk tersimpan: {len(df)}")
    return len(df)

@app.route("/scrape", methods=["GET"])
def scrape_route():
    count = scrape_prodnetafarm()
    return jsonify({
        "status": "success",
        "message": f"{count} produk berhasil disimpan."
    })


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))  # Gunakan env var untuk port (Railway set PORT otomatis)
    app.run(host="0.0.0.0", port=port, debug=False)  # debug=False untuk production
