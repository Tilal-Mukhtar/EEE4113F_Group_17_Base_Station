import requests

url = "http://raspberrypi.local/post_image.php"
api_key_value = "f29b28e9-5215-44db-9257-84d4e46d6371"

for i in range(1, 21):
    file_name = "image_" + str(i) + ".jpg"
    file = open("sample_images/" + file_name, "rb")
    data = {"api_key": api_key_value,
            "file_name": file_name, "file": file.read()}
    result = requests.post(url, data)
    print(result.text)
    file.close()

url = "http://raspberrypi.local/post_image_data.php"

current_date = "2024-05-02"
current_time = "19:30:00"
date = ["2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01", "2024-05-01",
        "2024-05-01", "2024-05-02", "2024-05-02", "2024-05-02", "2024-05-02", "2024-05-02", "2024-05-02", "2024-05-02", "2024-05-02", "2024-05-02"]
time = ["10:05:00", "10:10:00", "10:12:00", "13:51:00", "13:17:00", "14:02:00", "16:56:00", "17:03:00", "17:05:00", "17:11:00",
        "17:13:00", "09:59:00", "10:03:00", "13:04:00", "13:09:00", "13:12:00", "17:49:00", "17:53:00", "17:58:00", "18:01:00"]
file_name = ["image_1.jpg", "image_2.jpg", "image_3.jpg", "image_4.jpg", "image_5.jpg", "image_6.jpg", "image_7.jpg", "image_8.jpg", "image_9.jpg", "image_10.jpg",
             "image_11.jpg", "image_12.jpg", "image_13.jpg", "image_14.jpg", "image_15.jpg", "image_16.jpg", "image_17.jpg", "image_18.jpg", "image_19.jpg", "image_20.jpg"]
false_trigger = [0, 0, 1, 0, 0, 0, 0, 0,
                 0, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 1]

data = {"api_key": api_key_value, "current_date": current_date, "current_time": current_time,
        "date[]": date, "time[]": time, "file_name[]": file_name, "false_trigger[]": false_trigger}
result = requests.post(url, data)
print(result.text)
