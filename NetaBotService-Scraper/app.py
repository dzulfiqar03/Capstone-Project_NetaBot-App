from flask import Flask, jsonify
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from bs4 import BeautifulSoup
import pandas as pd
import threading, time, re, json, os, requests

app = Flask(__name__)

API_ENDPOINT = os.environ.get("API_ENDPOINT")
BASE_URL = "https://www.tokopedia.com/netafarm/product/page/{}"

# ------------------------
# Preload driver global
# ------------------------
def create_driver():
    chrome_options = Options()
    chrome_options.add_argument("--headless=new")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--blink-settings=imagesEnabled=false")
    chrome_options.add_argument("--disable-extensions")
    chrome_options.add_argument("--window-size=1920,1080")

    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()),
        options=chrome_options
    )
    return driver

driver = create_driver()

# ------------------------
# Fungsi scraping
# ------------------------
def scrape_prodnetafarm(max_scrape=50):
    wait = WebDriverWait(driver, 20)
    current_page = 1
    total_scraped = 0
    all_data = []

    while total_scraped < max_scrape:
        driver.get(BASE_URL.format(current_page))
        time.sleep(2)  # tunggu page load

        # scroll sampai habis
        last_height = driver.execute_script("return document.body.scrollHeight")
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1)
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height

        # ambil link product
        products = driver.find_elements(By.CSS_SELECTOR, "div.css-tjjb18 div.css-79elbk > a")
        links = [p.get_attribute("href") for p in products]

        for link in links:
            if total_scraped >= max_scrape:
                break
            try:
                driver.get(link)
                wait.until(EC.presence_of_element_located(
                    (By.CSS_SELECTOR, "h1[data-testid='lblPDPDetailProductName']")
                ))

                soup = BeautifulSoup(driver.page_source, "html.parser")
                nama = soup.find("h1").text.strip()
                harga_text = soup.find("div", {"data-testid": "lblPDPDetailProductPrice"}).text
                harga = int(re.sub(r'[^0-9]', '', harga_text))

                rating = "0"
                sold = "0"

                json_script = soup.find("script", {"id": "pdp-script"})
                if json_script:
                    try:
                        data_json = json.loads(json_script.text)
                        rating = str(data_json.get("rating", {}).get("score", "0"))
                        sold = str(data_json.get("txStats", {}).get("countSold", "0"))
                    except:
                        pass

                data = {
                    "name": nama,
                    "price": harga,
                    "rating": rating,
                    "sold": sold,
                    "link": link
                }

                # Kirim ke API kalau ada
                if API_ENDPOINT:
                    try:
                        requests.post(API_ENDPOINT, json=data, timeout=10)
                    except:
                        pass

                all_data.append(data)
                total_scraped += 1

            except Exception as e:
                print("Error scraping product:", e)

        current_page += 1

    print(f"Scrape finished: {total_scraped} products")
    return total_scraped

# ------------------------
# Route async scraping
# ------------------------
@app.route("/scrape", methods=["GET"])
def scrape():
    threading.Thread(target=scrape_prodnetafarm).start()
    return jsonify({"status": "scrape started"}), 202

# ------------------------
if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8080, threaded=True)
