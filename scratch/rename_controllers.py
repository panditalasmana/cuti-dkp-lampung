import os
import subprocess

cwd = r"c:\Users\pandi\Downloads\Cuti-DKP-Lampung"

# Move admin -> AdminTemp -> Admin
os.rename(os.path.join(cwd, "app", "Http", "Controllers", "admin"), os.path.join(cwd, "app", "Http", "Controllers", "AdminTemp"))
os.rename(os.path.join(cwd, "app", "Http", "Controllers", "AdminTemp"), os.path.join(cwd, "app", "Http", "Controllers", "Admin"))

# Move pegawai -> PegawaiTemp -> Pegawai
os.rename(os.path.join(cwd, "app", "Http", "Controllers", "pegawai"), os.path.join(cwd, "app", "Http", "Controllers", "PegawaiTemp"))
os.rename(os.path.join(cwd, "app", "Http", "Controllers", "PegawaiTemp"), os.path.join(cwd, "app", "Http", "Controllers", "Pegawai"))

print("Rename complete!")
