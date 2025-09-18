import requests
import json
from pathlib import Path
import random
import time

# =======================
# CONFIG
# =======================
API_URL = "http://localhost:8000/api/v1/admin/recognition/create"
BEARER_TOKEN = "1|o5paQ5GatIRSqI2GU9SErmYpoUMqAa2AsJNxXQhhac6dad6d"

# All dummy recognitions will use these same files/images
IMAGE_PATHS = [
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\pic1.jpg",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\pic2.jpg",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\pic3.jpg",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\pic4.jpg",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\pic5.gif",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\pic6.jpg",
]

FILE_PATHS = [
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\file1.pdf",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\file2.pdf",
    r"C:\Users\Jhon Andrie\OneDrive\Desktop\testfiles\file3.pdf"
]

# =======================
# OFFICIAL GOVERNMENT OFFICES
# =======================
OFFICES = [
    "OFFICE OF THE CITY ACCOUNTANT",
    "OFFICE OF THE CITY ADMINISTRATOR",
    "OFFICE OF THE CITY AGRICULTURIST",
    "OFFICE OF THE CITY ARCHITECT",
    "OFFICE OF THE CITY ASSESSOR",
    "OFFICE OF THE CITY BUDGET OFFICER",
    "OFFICE OF THE CITY CIVIL REGISTRAR",
    "OFFICE OF THE CITY DISASTER RISK REDUCTION AND MANAGEMENT OFFICER",
    "OFFICE OF THE CITY ECONOMIC ENTERPRISES MANAGER",
    "OFFICE OF THE CITY ENGINEER",
    "OFFICE OF THE CITY ENVIRONMENT AND NATURAL RESOURCES OFFICER",
    "OFFICE OF THE CITY GENERAL SERVICES OFFICER",
    "OFFICE OF THE CITY HEALTH OFFICER",
    "OFFICE OF THE CITY HOUSING AND LAND MANAGEMENT OFFICER",
    "OFFICE OF THE CITY HUMAN RESOURCE MANAGEMENT OFFICER",
    "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
    "OFFICE OF THE CITY LEGAL OFFICER",
    "OFFICE OF THE CITY MAYOR",
    "OFFICE OF THE CITY PLANNING AND DEVELOPMENT COORDINATOR",
    "OFFICE OF THE CITY PUBLIC EMPLOYMENT SERVICES AND CAPABILITY DEVELOPMENT OFFICER",
    "OFFICE OF THE CITY SOCIAL WELFARE AND DEVELOPMENT OFFICER",
    "OFFICE OF THE CITY TOURISM, ARTS, CULTURE AND HERITAGE MANAGEMENT OFFICER",
    "OFFICE OF THE CITY TREASURER",
    "OFFICE OF THE CITY VETERINARIAN",
    "OFFICE OF THE CITY VICE MAYOR",
    "OFFICE OF THE CITY VICE MAYOR (SP LEGISLATIVE)",
    "OFFICE OF THE CITY VICE MAYOR / SANGGUNIANG PANLUNGSOD",
    "OFFICE OF THE SANGGUNIANG PANLUNGSOD",
    "OFFICE OF THE SECRETARY TO THE SANGGUNIAN"
]

# =======================
# DUMMY DATA (Realistic government employee achievements)
# =======================
characters = [
    {"name": "Maria Santos", "type": "Outstanding Performance", "achievement": "Successfully implemented new financial reporting system that improved efficiency by 40%"},
    {"name": "Juan Dela Cruz", "type": "Leadership Excellence", "achievement": "Led the team in completing the annual budget proposal 2 weeks ahead of schedule"},
    {"name": "Ana Reyes", "type": "Innovation", "achievement": "Developed innovative agricultural program that increased local farmer yields by 25%"},
    {"name": "Carlos Garcia", "type": "Service Milestone", "achievement": "Completed 15 years of dedicated service to the city planning department"},
    {"name": "Elena Torres", "type": "Certification", "achievement": "Obtained professional certification in disaster risk management"},
    {"name": "Miguel Lopez", "type": "Academic Achievement", "achievement": "Completed Master's degree in Public Administration while maintaining full-time work"},
    {"name": "Sofia Ramirez", "type": "Cost Savings", "achievement": "Identified and implemented cost-saving measures saving the city ₱2.5 million annually"},
    {"name": "Antonio Mendoza", "type": "Project Excellence", "achievement": "Managed the successful completion of the new public infrastructure project under budget"},
    {"name": "Isabel Cruz", "type": "Community Service", "achievement": "Organized community outreach program benefiting 500+ residents"},
    {"name": "Ricardo Lim", "type": "Process Improvement", "achievement": "Streamlined civil registration process reducing processing time from 5 days to 1 day"},
    {"name": "Lourdes Chavez", "type": "Emergency Response", "achievement": "Coordinated effective emergency response during recent natural disaster"},
    {"name": "Fernando Gutierrez", "type": "Technology Implementation", "achievement": "Successfully migrated city systems to new digital platform with zero downtime"},
    {"name": "Carmen Villanueva", "type": "Health Initiative", "achievement": "Implemented public health program that reached 10,000 residents"},
    {"name": "Roberto Navarro", "type": "Environmental Stewardship", "achievement": "Led city-wide recycling initiative that reduced waste by 30%"},
    {"name": "Teresa Ong", "type": "Customer Service", "achievement": "Received 98% positive feedback in citizen satisfaction surveys"},
    {"name": "Alfredo Tan", "type": "Legal Expertise", "achievement": "Successfully handled complex legal cases saving the city from potential liabilities"},
    {"name": "Gloria Sy", "type": "Revenue Generation", "achievement": "Developed new revenue streams increasing city income by 15%"},
    {"name": "Armando Reyes", "type": "Infrastructure Development", "achievement": "Oversaw construction of 5 new public facilities completed on schedule"},
    {"name": "Corazon Lee", "type": "Social Welfare", "achievement": "Expanded social services program to cover 2,000 additional beneficiaries"},
    {"name": "Arturo Chan", "type": "Tourism Promotion", "achievement": "Increased tourist arrivals by 35% through innovative marketing campaigns"},
    {"name": "Beatriz Wong", "type": "Animal Welfare", "achievement": "Implemented successful spay/neuter program reducing stray animal population"},
    {"name": "Domingo Lim", "type": "Legislative Excellence", "achievement": "Authored 10 successful ordinances benefiting the community"},
    {"name": "Esperanza Tan", "type": "Human Resources", "achievement": "Reduced employee turnover by 20% through improved workplace initiatives"},
    {"name": "Felipe Ong", "type": "ICT Innovation", "achievement": "Developed mobile app for citizen services with 50,000+ downloads"},
    {"name": "Gabriela Sy", "type": "Budget Management", "achievement": "Managed ₱500 million budget with 99.8% accuracy rate"},
    {"name": "Hector Navarro", "type": "Assessment Reform", "achievement": "Modernized property assessment system increasing accuracy and fairness"},
    {"name": "Imelda Reyes", "type": "General Services", "achievement": "Improved maintenance operations reducing equipment downtime by 40%"},
    {"name": "Josefina Tan", "type": "Housing Development", "achievement": "Facilitated construction of 200 affordable housing units"},
    {"name": "Leonardo Lim", "type": "Public Employment", "achievement": "Placed 1,000 residents in gainful employment through job fairs"}
]

# =======================
# HELPER: upload files to pre-signed S3 URLs
# =======================
def upload_file(file_path, presigned_url):
    try:
        with open(file_path, "rb") as f:
            file_content = f.read()

        # For S3 pre-signed URLs, we need to send the file content as raw bytes in the body
        headers = {
            'Content-Type': 'application/octet-stream',
        }

        response = requests.put(presigned_url, data=file_content, headers=headers, timeout=30)

        if response.status_code in (200, 201, 204):
            print(f"✓ Uploaded: {Path(file_path).name} (Status: {response.status_code})")
            return True
        else:
            print(f"✗ Failed to upload {Path(file_path).name}: Status {response.status_code}, Response: {response.text}")
            return False

    except Exception as e:
        print(f"[ERROR] Failed to upload {file_path}: {e}")
        return False

statuses = ['pending', 'approved', 'rejected']

# =======================
# CREATE DUMMY RECOGNITIONS
# =======================
for idx, char in enumerate(characters, start=1):
    # Assign office - cycle through offices if we have more characters than offices
    office = OFFICES[(idx - 1) % len(OFFICES)]

    payload = {
        "status": random.choice(statuses),
        "employeeId": 10000 + idx,
        "employeeDepartment": office,
        "employeeName": char["name"],
        "recognitionDate": f"2025-08-{15 + (idx % 15):02d}",
        "recognitionType": char["type"],
        "achievementDescription": char["achievement"],
        "title" : "Hotdog",
        "images": [Path(p).name for p in IMAGE_PATHS],
        "files": [Path(p).name for p in FILE_PATHS]
    }

    print(f"\n[INFO] Sending createRecognition request for {payload['employeeName']} from {office}...")

    try:
        headers = {
            "Content-Type": "application/json",
            "Authorization": f"Bearer {BEARER_TOKEN}"
        }

        create_resp = requests.post(API_URL, headers=headers, json=payload, timeout=30)

        if create_resp.status_code != 200:
            print(f"[ERROR] Create recognition failed ({create_resp.status_code}): {create_resp.text}")
            continue

        resp_data = create_resp.json()
        print("[SUCCESS] Created:", payload["employeeName"])

        # Upload files using the pre-signed URLs
        if "data" in resp_data:
            data = resp_data["data"]

            # Upload images
            if "images" in data and data["images"]:
                print("Uploading images...")
                image_urls = data["images"]
                for path, url in zip(IMAGE_PATHS, image_urls):
                    if upload_file(path, url):
                        print(f"✓ Successfully uploaded image: {Path(path).name}")
                    else:
                        print(f"✗ Failed to upload image: {Path(path).name}")

            # Upload files
            if "files" in data and data["files"]:
                print("Uploading files...")
                file_urls = data["files"]
                for path, url in zip(FILE_PATHS, file_urls):
                    if upload_file(path, url):
                        print(f"✓ Successfully uploaded file: {Path(path).name}")
                    else:
                        print(f"✗ Failed to upload file: {Path(path).name}")

            # Add a small delay to avoid overwhelming the server
            time.sleep(0.5)

    except requests.exceptions.RequestException as e:
        print(f"[ERROR] Network error for {char['name']}: {e}")
    except Exception as e:
        print(f"[ERROR] Unexpected error for {char['name']}: {e}")

print(f"\n[DONE] Created {len(characters)} dummy recognitions across {len(OFFICES)} different offices.")
