import time  # Untuk menghitung waktu eksekusi
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
start_time = time.time()
driver = webdriver.Chrome()  
driver.maximize_window()
hasil_task = []
try:
    driver.get("http://localhost/websitecafe/login.php")  # Ganti URL dengan halaman login Anda
    WebDriverWait(driver, 50).until(EC.presence_of_element_located((By.NAME, "username")))
    username_field = driver.find_element(By.NAME, "username")
    username_field.send_keys("ss")
    password_field = driver.find_element(By.NAME, "password")
    password_field.send_keys("wrongpassword")
    captcha_field = driver.find_element(By.NAME, "captcha")
    captcha_field.send_keys("123rs")  
    login_button = driver.find_element(By.CLASS_NAME, "login-button")  
    login_button.click()
    error_message = WebDriverWait(driver, 50).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "p.error"))
    )
    if error_message.is_displayed():
        hasil_task.append(f"Login gagal! Pesan kesalahan: {error_message.text}")
    else:
        hasil_task.append("Login gagal! Tidak ada pesan kesalahan yang ditemukan.")
except Exception as e:
    hasil_task.append(f"Terjadi kesalahan: {e}")
finally:
    driver.quit()
    end_time = time.time()
    execution_time = end_time - start_time
    print(f"Times : {execution_time:.2f} detik")
    print("Hasil Task :")
    for task in hasil_task:
        print(f"- {task}")
