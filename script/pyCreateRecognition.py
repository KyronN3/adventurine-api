import requests
import json
from pathlib import Path
import random

# =======================
# CONFIG
# =======================
API_URL = "http://localhost:8000/api/v1/admin/recognition/create"

# All dummy recognitions will use these same files/images
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
# DUMMY DATA (character + lore-related achievements)
# =======================
characters = [
    {"name": "Aizen Sosuke", "department": "Department of Schemes", "type": "Mastermind of the Month", "achievement": "Orchestrated Ichigo's growth to perfection, manipulating all events flawlessly."},
    {"name": "Gilgamesh", "department": "Department of Heroic Spirits", "type": "Golden Emperor Award", "achievement": "Claimed every artifact in sight, proving he's the king of everything."},
    {"name": "Saber", "department": "Department of Heroic Spirits", "type": "Loyal Servant Award", "achievement": "Fought countless wars for honor and never wavered from duty."},
    {"name": "Rin Tohsaka", "department": "Department of Espionage", "type": "Cleverest Mage Award", "achievement": "Managed magic and school life simultaneously, always one step ahead."},
    {"name": "Shirou Emiya", "department": "Department of Self-Sacrifice", "type": "Chivalry Award", "achievement": "Risked everything to save others, even when it meant burning out."},
    {"name": "Hoshino Ai", "department": "Department of Idol Management", "type": "Rising Star", "achievement": "Navigated the idol industry with charm, talent, and resilience."},
    {"name": "Aqua Hoshino", "department": "Department of Idols", "type": "Support Star", "achievement": "Kept the family and fans entertained despite constant chaos."},
    {"name": "Ruby Hoshino", "department": "Department of Fun", "type": "Cheerfulness Award", "achievement": "Brought joy and positivity wherever she went."},
    {"name": "Makima", "department": "Department of Control", "type": "Manipulation Award", "achievement": "Influenced everyone and everything to achieve her goals silently."},
    {"name": "Power", "department": "Department of Chaos", "type": "Chaotic Energy Award", "achievement": "Turned disorder into fun while still achieving objectives."},
    {"name": "Denji", "department": "Department of Persistence", "type": "Chainsaw Master Award", "achievement": "Survived impossible odds using guts, chainsaw, and sheer luck."},
    {"name": "Gojo Satoru", "department": "Department of Overpowered", "type": "Infinity Award", "achievement": "Handled every threat with overwhelming power and style."},
    {"name": "Yor Forger", "department": "Department of Espionage", "type": "Silent Assassin Award", "achievement": "Completed every mission flawlessly while maintaining perfect domestic life."},
    {"name": "Illyasviel von Einzbern", "department": "Department of Magic", "type": "Cuteness Overload Award", "achievement": "Used magical abilities for maximum impact while appearing innocent."},
    {"name": "Taishi Gotanda", "department": "Department of Plot Twists", "type": "Unexpected Hero Award", "achievement": "Surprised everyone with heroic deeds when least expected."}
]

# =======================
# HELPER: upload files (dummy, backend ignores actual content)
# =======================
def upload_file(file_path, url):
    with open(file_path, "rb") as f:
        r = requests.put(url, data=f)
    return r.status_code in (200, 201)

statuses = ['pending', 'accepted', 'rejected']

# =======================
# CREATE DUMMY RECOGNITIONS
# =======================
for idx, char in enumerate(characters, start=1):
    payload = {
        "status": random.choice(statuses),
        "employeeId": 100 + idx,
        "employeeDepartment": char["department"],
        "employeeName": char["name"],
        "recognitionDate": f"2025-08-{14 + (idx % 15):02d}",
        "recognitionType": char["type"],
        "achievementDescription": char["achievement"],
        "images": [{"name": Path(p).name, "file": open(p, "rb").read()} for p in IMAGE_PATHS],
        "files": [{"name": Path(p).name, "file": open(p, "rb").read()} for p in FILE_PATHS]
    }

    print(f"\n[INFO] Sending createRecognition request for {payload['employeeName']}...")

    create_resp = requests.post(API_URL, headers={"Content-Type": "application/json"}, json=payload)

    if create_resp.status_code != 200:
        print("[ERROR] Create recognition failed:", create_resp.status_code, create_resp.text)
        continue

    resp_data = create_resp.json()
    print("[SUCCESS] Created:", payload["employeeName"])

    # Upload files (all the same for every user)
    image_urls = resp_data["data"]["images"]
    file_urls = resp_data["data"]["files"]

    for path, url in zip(IMAGE_PATHS, image_urls):
        upload_file(path, url)
    for path, url in zip(FILE_PATHS, file_urls):
        upload_file(path, url)

print("\n[DONE] All 15 dummy recognitions created & files uploaded.")
