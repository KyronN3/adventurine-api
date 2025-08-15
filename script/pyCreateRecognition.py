import requests
import json
from pathlib import Path

# =======================
# CONFIG
# =======================
API_URL = "http://localhost:8000/api/v1/admin/recognition/create"  # Your actual API endpoint

# Local paths to your files
IMAGE_PATHS = [
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\3ji8t0l480ce1.jpg",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\suddenly-im-a-tcg-player-again-v0-6xzxgssxmf6c1.jpg",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\t1zyxhcvao2e1.jpg"
]
FILE_PATHS = [
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\CANEDO_ProjectProposal.pdf",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\Big-Paws-Business-Requirements.pdf",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\Big_Paws_Pet_Hotel_GanttChart.pdf"
]

# =======================
# 1. Send create request
# =======================
payload = {
    "employeeId": 153,
    "employeeDepartment": "Department of Brainrot",
    "employeeName": "Akane Kurokawa",
    "recognitionDate": "2025-08-14",
    "recognitionType": "Employee of the Month",
    "achievementDescription": "Successfully led the social media campaign to increase engagement by 50%.",
    "images": [Path(p).name for p in IMAGE_PATHS],  # send only file names
    "files": [Path(p).name for p in FILE_PATHS]
}

print("[INFO] Sending createRecognition request...")
create_resp = requests.post(
    API_URL,
    headers={
        "Content-Type": "application/json"
    },
    json=payload
)

if create_resp.status_code != 200:
    print("[ERROR] Create recognition failed:", create_resp.status_code, create_resp.text)
    exit(1)

resp_data = create_resp.json()
print("[INFO] API Response:\n", json.dumps(resp_data, indent=2))

# =======================
# 2. Upload files to presigned URLs
# =======================
def upload_file(file_path, url):
    print(f"[INFO] Uploading {file_path}...")
    with open(file_path, "rb") as f:
        r = requests.put(url, data=f)
    if r.status_code in (200, 201):
        print("[SUCCESS] Uploaded:", file_path)
    else:
        print(f"[ERROR] Upload failed for {file_path}: {r.status_code} - {r.text}")

image_urls = resp_data["data"]["images"]
file_urls = resp_data["data"]["files"]

# Ensure same order of paths and URLs
for path, url in zip(IMAGE_PATHS, image_urls):
    upload_file(path, url)

for path, url in zip(FILE_PATHS, file_urls):
    upload_file(path, url)

print("[DONE] All uploads complete.")
