import os

workspace_dir = r"c:\Users\m s i\Documents\coba coba\web klinik"

for root, dirs, files in os.walk(workspace_dir):
    for file in files:
        if any(term in file.lower() for term in ["backup", "sidebar", "layout", "old", "temp"]):
            filepath = os.path.join(root, file)
            print(f"File: {filepath} ({os.path.getsize(filepath)} bytes)")
