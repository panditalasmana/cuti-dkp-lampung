import os
import shutil

base_path = r"c:\Users\pandi\Downloads\Cuti-DKP-Lampung\app\Http\Controllers"

admin_old = os.path.join(base_path, "admin")
admin_temp = os.path.join(base_path, "AdminTemp")
admin_new = os.path.join(base_path, "Admin")

pegawai_old = os.path.join(base_path, "pegawai")
pegawai_temp = os.path.join(base_path, "PegawaiTemp")
pegawai_new = os.path.join(base_path, "Pegawai")

if os.path.exists(admin_old):
    shutil.move(admin_old, admin_temp)
    shutil.move(admin_temp, admin_new)
    print("Admin folder renamed!")

if os.path.exists(pegawai_old):
    shutil.move(pegawai_old, pegawai_temp)
    shutil.move(pegawai_temp, pegawai_new)
    print("Pegawai folder renamed!")
