import sys
import os
import pandas as pd
import requests
from bs4 import BeautifulSoup

def buscar_videos_youtube():

    YOUTUBE_API_KEY = 'SUA_CHAVE_API_AQUI'  # valor real apenas no servidor, não commitado
    url = 'https://www.googleapis.com/youtube/v3/videos'


    categorias = {
        'Música': '10',
        'Games': '20',
        'Ciência e Tecnologia': '28',
        'Esportes': '17'
    }
    
    all_video_data = []

    for nome_categoria, id_categoria in categorias.items():
        payload = {
            'part': 'snippet,statistics',
            'chart': 'mostPopular',
            'videoCategoryId': id_categoria,
            'regionCode': 'BR',  
            'maxResults': 5,     
            'key': YOUTUBE_API_KEY
        }
        
        response = requests.get(url, params=payload)
        
        if response.status_code == 200:
            data = response.json()
            items = data.get('items', [])
            
            for item in items:
                snippet = item.get('snippet', {})
                statistics = item.get('statistics', {})
                video_id = item.get('id', '')
                
                all_video_data.append({
                    'Categoria': nome_categoria,
                    'Título do Vídeo': snippet.get('title', ''),
                    'Canal': snippet.get('channelTitle', ''),
                    'Visualizações': int(statistics.get('viewCount', 0)),
                    'Likes': int(statistics.get('likeCount', 0)),
                    'Link': f'https://www.youtube.com/watch?v={video_id}'
                })

    if all_video_data:
        df = pd.DataFrame(all_video_data)
        

        df = df.sort_values(by='Visualizações', ascending=False)
        
        os.makedirs("static", exist_ok=True)
        caminho_arquivo = "static/api_youtube_resultado.xlsx"
        df.to_excel(caminho_arquivo, index=False, engine="openpyxl")


def fazer_scraping():
    url = "https://www.ibyte.com.br/pcs-e-notebooks/computador"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36"}

    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        soup = BeautifulSoup(response.text, "html.parser")
        produtos = []

        for produto in soup.find_all("div", class_="flex flex-row min-h-full relative w-full overflow-hidden bg-white shadow rounded transition-shadow h-full md:flex-col hover:shadow-md"):
            try:
                nome = produto.find("h2", class_="text-gray-800").text.strip()
                preco = produto.find("span", class_="text-verde-500 js-best-price").text.strip()
                desconto = produto.find("p", class_="flex flag js-discount-flag")
                desconto = desconto.text.strip() if desconto else "Sem desconto importante"

                produtos.append({"Nome": nome, "Preço": preco, "Desconto": desconto})
            except AttributeError:
                continue

        df = pd.DataFrame(produtos)
        os.makedirs("static", exist_ok=True)
        df.to_excel("static/Web_Scrapping_resultado.xlsx", index=False, engine="openpyxl")


if __name__ == "__main__":
    tipo = sys.argv[1] if len(sys.argv) > 1 else ""

    if tipo == "api_youtube":
        buscar_videos_youtube()
    elif tipo == "Web_Scrapping_":
        fazer_scraping()