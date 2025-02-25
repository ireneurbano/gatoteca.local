import time
import pymysql
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Función para conectar con la base de datos MySQL usando pymysql
def conectar_db():
    db_config = {
        'host': 'localhost',
        'user': 'root',
        'password': 'usuario',
        'database': 'gatoteca',
    }
    print("Conectando a la base de datos...")
    return pymysql.connect(**db_config)

# Función para configurar el driver de Selenium
def configurar_driver():
    print("Configurando el driver de Selenium...")
    servicio = Service('/usr/bin/chromedriver')  # Ruta correcta a chromedriver
    opciones = Options()
    opciones.add_argument("--headless")  # Ejecutar en modo sin interfaz gráfica
    return webdriver.Chrome(service=servicio, options=opciones)

# Función para verificar si un carácter ya existe en la base de datos
def caracter_existe(cursor, caracter):
    query = "SELECT COUNT(*) FROM Caracter WHERE caracter = %s"
    cursor.execute(query, (caracter,))
    return cursor.fetchone()[0] > 0

# Función para obtener los datos de las razas
def obtener_datos():
    # Configura el driver
    driver = configurar_driver()

    # URL del sitio web con las razas
    url = 'https://www.expertoanimal.com/razas-de-gatos.html'
    print(f"Accediendo a la URL: {url}")
    driver.get(url)

    # Espera para que la página cargue
    print("Esperando a que la página cargue...")
    WebDriverWait(driver, 15).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, 'div.resultado a'))
    )

    # Encuentra todos los enlaces a las razas
    print("Buscando enlaces a las razas...")
    razas = driver.find_elements(By.CSS_SELECTOR, 'div.resultado a')

    # Conectar a la base de datos
    conexion = conectar_db()
    cursor = conexion.cursor()

    # Itera sobre los resultados y guarda los datos en la base de datos
    for raza in razas:
        try:
            # Abre cada enlace de raza en una nueva pestaña y cambia de pestaña
            link = raza.get_attribute('href')
            driver.execute_script("window.open(arguments[0]);", link)
            driver.switch_to.window(driver.window_handles[1])

            WebDriverWait(driver, 15).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, 'h1'))
            )

            print(f"Procesando raza: {link}")

            # Esperar específicamente a que los caracteres sean visibles
            try:
                WebDriverWait(driver, 15).until(
                    EC.presence_of_element_located((By.CSS_SELECTOR, 'div.elemento.el--generic .titulo.titulo--infografia'))
                )
                print("Sección 'Carácter' visible.")

                # Ahora buscamos los elementos correspondientes a los caracteres
                divs_genericos = driver.find_elements(By.CSS_SELECTOR, 'div.elemento.el--generic')
                caracteres = set()  # Usamos un set para eliminar duplicados

                for div in divs_genericos:
                    try:
                        # Revisamos el título de la sección para asegurarnos que es "Carácter"
                        titulo = div.find_element(By.CSS_SELECTOR, 'div.titulo.titulo--infografia').text.strip()
                        if "Carácter" in titulo:  # Nos aseguramos de que estamos en la sección de "Carácter"
                            # Ahora buscamos los caracteres dentro de los <a> dentro de <li>
                            for li in div.find_elements(By.CSS_SELECTOR, 'div.prop-ig--col ul li a'):
                                caracter_limpio = li.text.strip()
                                if caracter_limpio:  # Evitar valores vacíos
                                    caracteres.add(caracter_limpio)
                    except Exception as e:
                        print(f"Error procesando un div de carácter: {e}")

                if not caracteres:
                    caracteres.add("Carácter no disponible")

                print(f"Carácter encontrado: {caracteres}")

                # Guardar cada carácter en la BD si no existe
                for caracter in caracteres:
                    if not caracter_existe(cursor, caracter):
                        query = "INSERT INTO Caracter (titulo, caracter) VALUES (%s, %s)"
                        cursor.execute(query, ("Carácter", caracter))
                        conexion.commit()
                        print(f'Carácter "{caracter}" guardado correctamente.')
                    else:
                        print(f'Carácter "{caracter}" ya existe en la base de datos, omitiendo.')

            except Exception as e:
                print(f"Error al obtener los caracteres: {e}")

            # Cierra la pestaña actual y regresa a la pestaña principal
            driver.close()
            driver.switch_to.window(driver.window_handles[0])
        except Exception as e:
            print(f'Error al procesar la raza: {e}')
            driver.close()
            driver.switch_to.window(driver.window_handles[0])

    # Cierra la conexión y el driver
    print("Cerrando la conexión a la base de datos...")
    cursor.close()
    conexion.close()
    driver.quit()

# Llamada a la función para obtener los datos
print("Iniciando la obtención de datos...")
obtener_datos()
print("Proceso completado.")
