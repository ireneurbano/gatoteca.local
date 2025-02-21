import requests
from bs4 import BeautifulSoup
import mysql.connector

# Conexión a la base de datos
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="usuario",
    database="gatoteca"
)

cursor = db.cursor()

# URL de la página principal que quieres hacer scraping
url = "https://www.expertoanimal.com/razas-de-gatos.html"

# Realiza la solicitud HTTP a la página principal
response = requests.get(url)

# Comprobar que la página fue descargada correctamente
if response.status_code != 200:
    print(f"Error al descargar la página. Status code: {response.status_code}")
    exit()

print("Página principal descargada correctamente")

soup = BeautifulSoup(response.content, "html.parser")

# Encuentra todos los enlaces a razas de gatos
razas = soup.find_all("div", class_="resultado link")

# Verificar si encontramos razas
if not razas:
    print("No se encontraron razas en la página")
else:
    print(f"Se encontraron {len(razas)} razas en la página")

# Itera sobre cada raza y entra a la página de la raza
for raza in razas:
    # Encuentra el enlace a la página de la raza
    enlace = raza.find("a", class_="titulo titulo--resultado")
    if enlace:
        url_raza = enlace["href"]
        print(f"Entrando a la página de la raza: {url_raza}")
        
        # Realiza la solicitud HTTP a la página de la raza
        response_raza = requests.get(url_raza)
        
        if response_raza.status_code != 200:
            print(f"Error al descargar la página de la raza. Status code: {response_raza.status_code}")
            continue
        
        # Analiza la página de la raza
        soup_raza = BeautifulSoup(response_raza.content, "html.parser")
        
        # Extrae la sección de origen
        origen_section = soup_raza.find("div", class_="elemento el--generic")
        
        if origen_section:
            origen_title = origen_section.find("div", class_="titulo titulo--infografia").text.strip()
            origen_list = origen_section.find("div", class_="prop-ig")
            
            if origen_list:
                paises = [li.text.strip() for li in origen_list.find_all("li")]
                if paises:
                    print(f"Orígenes encontrados para {url_raza}: {', '.join(paises)}")
                    for pais in paises:
                        # Verifica si el origen ya existe en la base de datos
                        cursor.execute("SELECT * FROM Origen WHERE origen = %s", (pais,))
                        if cursor.fetchone() is None:
                            # Inserta el nuevo origen si no existe
                            query = """
                            INSERT INTO Origen (titulo, origen)
                            VALUES (%s, %s)
                            """
                            cursor.execute(query, (origen_title, pais))
                            print(f"Origen insertado: {pais}")
                        else:
                            print(f"El origen {pais} ya existe en la base de datos")
                else:
                    print(f"No se encontraron países en la lista de orígenes para {url_raza}")
            else:
                print(f"No se encontró la lista de países de origen para {url_raza}")
        else:
            print(f"No se encontró la sección de 'Origen' en la página de la raza: {url_raza}")

# Confirma los cambios y cierra la conexión
db.commit()
cursor.close()
db.close()
