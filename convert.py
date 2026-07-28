import base64

p_admin = r"C:\Users\pandi\.gemini\antigravity\brain\3de0abd2-d98c-4319-a624-4e23c67abc9a\.user_uploaded\media__1785134293492.png"
p_pegawai = r"C:\Users\pandi\.gemini\antigravity\brain\3de0abd2-d98c-4319-a624-4e23c67abc9a\.user_uploaded\media__1785134658531.png"
p_login = r"C:\Users\pandi\.gemini\antigravity\brain\3de0abd2-d98c-4319-a624-4e23c67abc9a\.user_uploaded\media__1785133876394.png"
target = r"c:\Users\pandi\Downloads\portofolio\index.html"

def to_b64(path):
    with open(path, "rb") as f:
        return "data:image/png;base64," + base64.b64encode(f.read()).decode("utf-8")

b_admin = to_b64(p_admin)
b_pegawai = to_b64(p_pegawai)
b_login = to_b64(p_login)

with open(target, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("images/sipencuti-admin.png", b_admin)
content = content.replace("images/sipencuti-pegawai.png", b_pegawai)
content = content.replace("images/sipencuti-login.png", b_login)

with open(target, "w", encoding="utf-8") as f:
    f.write(content)

print("SUCCESSFULLY_UPDATED_HTML")
