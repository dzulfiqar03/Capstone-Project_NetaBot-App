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
    BASE_URL = "https://www.tokopedia.com/netafarm/product/page/{}"

    options = Options()
    options.binary_location = "/usr/bin/chromium"  # path Chromium di container
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")

    # Gunakan Service dengan chromedriver yang sudah diinstall via apt
    service = Service("/usr/bin/chromedriver")
    driver = webdriver.Chrome(service=service, options=options)
    wait = WebDriverWait(driver, 20)

    current_page = 1
    driver.get(BASE_URL.format(current_page))
    time.sleep(3)

    all_data = []
    total_scraped = 0
    MAX_SCRAPE = 200

    print("\n🚀 Mulai scraping NETAFARM...\n")

    # =========================
    # GET MAIN PRODUCT IMAGE
    # =========================
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

            url = re.search(r'url\$\"(.*)\"\$', bg)
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

    # =========================
    # MAIN LOOP
    # =========================
    while total_scraped < MAX_SCRAPE:

        print(f"=== Halaman {current_page} ===")

        last_height = driver.execute_script("return document.body.scrollHeight")
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1)
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height

        produk_elements = driver.find_elements(
            By.CSS_SELECTOR, "div.css-tjjb18 div.css-79elbk > a"
        )

        print(f"📦 {len(produk_elements)} produk ditemukan\n")

        if len(produk_elements) == 0:
            print("🚫 Tidak ada produk — berhenti.")
            break

        links = [p.get_attribute("href") for p in produk_elements]

        # LOOP PRODUK
        for link in links:

            if total_scraped >= MAX_SCRAPE:
                print("\n⏹ MAX produk tercapai! Reset ke page/1…")
                current_page = 1
                driver.get(BASE_URL.format(current_page))
                time.sleep(2)
                break

            try:
                driver.get(link)
                wait.until(EC.presence_of_element_located(
                    (By.CSS_SELECTOR, "h1[data-testid='lblPDPDetailProductName']")
                ))
                time.sleep(1)

                soup = BeautifulSoup(driver.page_source, "html.parser")

                # ---------------------------
                # AMBIL JSON TOKOPEDIA
                # ---------------------------
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

                # ---------------------------
                # FALLBACK SELECTOR TOKOPEDIA (sesuai HTML terbaru)
                # ---------------------------

                # SOLD
                if sold == "0":
                    try:
                        sold_elem = soup.find("p", {"data-testid": "lblPDPDetailProductSoldCounter"})
                        if sold_elem:
                            angka = re.findall(r'\d+', sold_elem.text)
                            if angka:
                                sold = angka[0]
                    except:
                        pass

                # RATING
                if rating == "0":
                    try:
                        rating_elem = soup.find("span", {"data-testid": "lblPDPDetailProductRatingNumber"})
                        if rating_elem:
                            rating = rating_elem.text.strip()
                    except:
                        pass

                # ---------------------------
                # Nama
                # ---------------------------
                nama = soup.find("h1", {"data-testid": "lblPDPDetailProductName"}).text.strip()

                # Harga
                harga_text = soup.find("div", {"data-testid": "lblPDPDetailProductPrice"}).text.strip()
                harga_num = int(re.sub(r'[^0-9]', '', harga_text))

                # Gambar
                gambar = get_product_image()

                # Deskripsi
                try:
                    desc_box = driver.find_element(
                        By.CSS_SELECTOR, "div[data-testid='lblPDPDescriptionProduk']"
                    )
                    deskripsi = BeautifulSoup(
                        desc_box.get_attribute("innerHTML"), "html.parser"
                    ).get_text(separator="\n").strip()
                except:
                    deskripsi = "Tidak ada deskripsi"

                # ------------------------
                # SIMPAN KE API
                # ------------------------
                data = {
                    "name": nama,
                    "price": harga_num,
                    "description": deskripsi,
                    "url_images": gambar,
                    "rating": rating,
                    "sold": sold,
                    "link": link
                }

                req = requests.post(API_ENDPOINT, json=data)
                status = "✅" if req.status_code == 200 else "⚠️"

                print(f"{status} {total_scraped + 1}. {nama}")
                print(f"   ⭐ Rating: {rating}   |   🟢 Terjual: {sold}")

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

            except Exception as e:
                print(f"❌ Error: {e}")
                continue

        if total_scraped >= MAX_SCRAPE:
            break

        current_page += 1
        print(f"➡️ Beralih ke halaman {current_page}")
        driver.get(BASE_URL.format(current_page))
        time.sleep(2)

    driver.quit()

    df = pd.DataFrame(all_data)
    df.to_excel("produk_netafarm.xlsx", index=False)

    print(f"\n🎉 Selesai! Total produk tersimpan: {len(df)}\n")

    return int(len(df))

@app.route("/scrape", methods=["GET", "POST"])
def scrape_route():
    count = scrape_prodnetafarm()
    return jsonify({
        "status": "success",
        "message": f"{count} produk berhasil disimpan."
    })


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))  # Gunakan env var untuk port (Railway set PORT otomatis)
    app.run(host="0.0.0.0", port=port, debug=False)  # debug=False untuk production
