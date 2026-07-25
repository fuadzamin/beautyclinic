import os

for root, dirs, files in os.walk('.'):
    for f in files:
        if f.endswith('.vue') or f.endswith('.js'):
            path = os.path.join(root, f)
            try:
                with open(path, 'r', encoding='utf-8') as file_obj:
                    content = file_obj.read()
                    if 'PosHistory' in content:
                        print(f"Found in: {path}")
            except Exception:
                pass
