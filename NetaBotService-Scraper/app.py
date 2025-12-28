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
import time, re, json, os, requests

app = Flask(__name__)

API_ENDPOINT = os.environ.get("API_ENDPOINT")
BASE_URL = "https://www.tokopedia.com/netafarm/product/page/{}"

def create_driver():
    options = Options()
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    options.add_argument("--disable-extensions")
    options.add_argument("--remote-debugging-port=9222")

    # webdriver-manager akan download ChromeDriver + optional Chromium
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=options)
    return driver

def scrape_prodnetafarm():
    driver = create_driver()
    wait = WebDriverWait(driver, 20)

    current_page = 1
    driver.get(BASE_URL.format(current_page))
    time.sleep(3)

    all_data = []
    total_scraped = 0
    MAX_SCRAPE = 50  # 🚨 JANGAN BESAR DI CLOUD

    while total_scraped < MAX_SCRAPE:

        last_height = driver.execute_script("return document.body.scrollHeight")
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1)
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height

        products = driver.find_elements(By.CSS_SELECTOR, "div.css-tjjb18 div.css-79elbk > a")
        links = [p.get_attribute("href") for p in products]

        for link in links:
            if total_scraped >= MAX_SCRAPE:
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

                if API_ENDPOINT:
                    requests.post(API_ENDPOINT, json=data, timeout=10)

                all_data.append(data)
                total_scraped += 1

            except Exception as e:
                print("Error:", e)

        current_page += 1
        driver.get(BASE_URL.format(current_page))
        time.sleep(2)

    driver.quit()
    return total_scraped

@app.route("/scrape", methods=["POST"])
def scrape():
    count = scrape_prodnetafarm()
    return jsonify({
        "status": "success",
        "total": count
    })

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 8080))
    app.run(host="0.0.0.0", port=port)
