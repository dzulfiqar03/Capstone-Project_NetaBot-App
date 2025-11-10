from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
from flask import Flask, render_template, request
import pandas as pd
import time
import requests
import re

app = Flask(__name__)

def scrape_prodnetafarm():
    API_ENDPOINT = 'http://localhost:8000/api/products'  # Ganti dengan API Laravel kamu
    url = "https://www.tokopedia.com/netafarm/product"

    # === Setup browser ===
    options = Options()
    options.add_argument("--start-maximized")
    driver = webdriver.Chrome(options=options)
    driver.get(url)
    wait = WebDriverWait(driver, 15)

    all_data = []
    page = 1

    print("🚀 Mulai scraping produk NETAFARM Tokopedia...\n")

    while True:
        print(f"=== Halaman {page} ===")

        # Scroll sampai semua produk termuat
        last_height = driver.execute_script("return document.body.scrollHeight")
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(2)
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height

        # Ambil semua link produk (CSS class stabil)
        produk_elements = driver.find_elements(By.CSS_SELECTOR, "a[class^='Ui5-B4CDAk4Cv-cjLm4o0g== XeGJAOdlJaxl4+UD3zEJLg==']")
        print(f"📦 Ditemukan {len(produk_elements)} produk di halaman {page}\n")

        # Simpan semua href (agar DOM tidak hilang ketika klik)
        produk_links = [p.get_attribute("href") for p in produk_elements]

        for idx, link in enumerate(produk_links):
            try:
                driver.get(link)
                wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "h1[data-testid='lblPDPDetailProductName']")))
                time.sleep(2)

                soup = BeautifulSoup(driver.page_source, "html.parser")

                # === Ambil data utama ===
                nama = soup.find('h1', {'data-testid': 'lblPDPDetailProductName'}).text.strip()
                harga = soup.find('div', {'data-testid': 'lblPDPDetailProductPrice'}).text.strip()
                harga_angka = int(re.sub(r'[^0-9]', '', harga))

                # === Ambil gambar utama ===
                try:
                    gambar = driver.find_element(By.CSS_SELECTOR, "img[data-testid='PDPMainImage']").get_attribute("src")
                except:
                    try:
                        gambar = soup.find('img', alt=re.compile("Gambar"))['src']
                    except:
                        gambar = "-"

                # === Lihat deskripsi (klik “Lihat Selengkapnya” kalau ada) ===
                try:
                    see_more = driver.find_element(By.CSS_SELECTOR, "button[data-testid='btnPDPSeeMore']")
                    driver.execute_script("arguments[0].click();", see_more)
                    time.sleep(1.5)
                except:
                    pass

                # === Ambil deskripsi ===
                try:
                    deskripsi_elem = driver.find_element(By.CSS_SELECTOR, "div[data-testid='lblPDPDescriptionProduk']")
                    deskripsi_html = deskripsi_elem.get_attribute("innerHTML")
                    deskripsi = BeautifulSoup(deskripsi_html, "html.parser").get_text(separator="\n").strip()
                except:
                    deskripsi = "Tidak ada deskripsi"

                data = {
                    'name': nama,
                    'price': harga_angka,
                    'description': deskripsi,
                    'url-images': gambar
                }

                # Kirim ke API Laravel
                r = requests.post(API_ENDPOINT, json=data)
                status = "✅" if r.status_code == 200 else f"⚠️ ({r.status_code})"
                print(f"{status} [{idx+1}] {nama}")

                # Simpan ke list untuk Excel
                all_data.append({
                    "No": len(all_data) + 1,
                    "Nama Produk": nama,
                    "Harga": harga,
                    "Deskripsi": deskripsi,
                    "Gambar": gambar,
                    "Link": link
                })

            except Exception as e:
                print(f"❌ Gagal produk ke-{idx+1}: {e}")
                continue

        # === Pindah ke halaman berikutnya jika ada tombol Next ===
        try:
            next_button = driver.find_element(By.CSS_SELECTOR, "a[data-testid^='btnShopProductPageNext']")
            driver.execute_script("arguments[0].click();", next_button)
            time.sleep(4)
            page += 1
        except:
            print("\n🚫 Tidak ada tombol Next lagi. Scraping selesai.")
            break

    driver.quit()

    # === Simpan hasil ke Excel ===
    df = pd.DataFrame(all_data)
    df.to_excel("produk_netafarm.xlsx", index=False)
    print(f"\n🎉 {len(df)} produk berhasil disimpan ke produk_netafarm.xlsx\n")

    return df


@app.route("/scrape", methods=["GET"])
def scrape_route():
    count = scrape_prodnetafarm()
    return jsonify({"status": "success", "message": f"{count} produk berhasil disimpan."})

if __name__ == "__main__":
    app.run(debug=True, port=5000)