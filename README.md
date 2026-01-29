Live Demo : https://veronica-advanced-bike-assistant.netlify.app/veronica.html (use Chrome for better Voice Response)

# Real-time Accident Detection System

This project implements a real-time accident detection system using ESP32 and MPU6050 gyroscope sensor. The system monitors bike movements and detects potential accidents based on sudden changes in angular velocity.

## Features

- Real-time gyroscope data monitoring (X, Y, Z rotation)
- WebSocket-based communication between ESP32 and web interface
- Visual and audio alerts for potential accidents
- Clean and modern UI with real-time updates

#Preview
 <img width="1905" height="1024" alt="image" src="https://github.com/user-attachments/assets/46d97312-7b41-48c1-bde7-7bdd52013b80" />
<img width="1912" height="1031" alt="image" src="https://github.com/user-attachments/assets/a33a584d-2abd-4308-ba89-6df669ec4b41" /> 
<img width="1919" height="997" alt="image" src="https://github.com/user-attachments/assets/cc29dcc4-7140-4a7b-a144-dd8b35768f3a" />
<img width="1919" height="1019" alt="image" src="https://github.com/user-attachments/assets/ea344786-ece5-4a1b-99f0-c2ac84126149" />
<img width="1919" height="1025" alt="image" src="https://github.com/user-attachments/assets/1c916e95-5b32-45fa-9a9b-fbc36dc0e13e" />
<img width="1919" height="1018" alt="image" src="https://github.com/user-attachments/assets/bfdd2024-cd29-4d42-b179-5a3cf653c7e6" />
<img width="1914" height="1023" alt="image" src="https://github.com/user-attachments/assets/5de8ad2e-32ce-46ed-b617-d1bf569c8ddc" />
<img width="1913" height="1034" alt="image" src="https://github.com/user-attachments/assets/7cb5af6a-ba90-4ec9-96ea-3e18a346effd" />




<img width="1919" height="1022" alt="image" src="https://github.com/user-attachments/assets/e6247878-b455-465e-aa12-6f9d0ea3f881" />
<img width="1910" height="1029" alt="image" src="https://github.com/user-attachments/assets/6963fa5c-7795-460c-8d0b-0c6517309dbd" />
<img width="1919" height="1022" alt="image" src="https://github.com/user-attachments/assets/2144fe12-fe5d-42e8-a5ec-2aa33ab76651" />
<img width="1910" height="1029" alt="image" src="https://github.com/user-attachments/assets/7adf9b83-08c2-4d50-a854-f4817da13e5d" />
<img width="1124" height="760" alt="image" src="https://github.com/user-attachments/assets/e9db5c6e-cc83-452a-8ccf-ee213bca0035" />


<img width="1907" height="938" alt="image" src="https://github.com/user-attachments/assets/b425280b-4c49-437e-a722-287f9db7cb1d" />

<img width="1919" height="1027" alt="image" src="https://github.com/user-attachments/assets/272f51c4-13cd-4039-b034-7beefc6968c4" />

<img width="1919" height="1029" alt="image" src="https://github.com/user-attachments/assets/6c7e420e-59d4-443e-bf54-7e58829b40a4" />






# Real-time Accident Detection System

This project implements a real-time accident detection system using ESP32 and MPU6050 gyroscope sensor. The system monitors bike movements and detects potential accidents based on sudden changes in angular velocity.

## Features

- Real-time gyroscope data monitoring (X, Y, Z rotation)
- WebSocket-based communication between ESP32 and web interface
- Visual and audio alerts for potential accidents
- Clean and modern UI with real-time updates

## Setup Instructions

1. Install Node.js dependencies:
```bash
npm install
```

2. Upload the ESP32 code to your device (see ESP32 setup below)

3. Start the WebSocket server:
```bash
node server.js
```

4. Open `veronica.html` in your web browser

## ESP32 Setup

1. Install the required libraries in Arduino IDE:
   - WebSocketsClient
   - ArduinoJson
   - MPU6050_tockn

2. Connect the MPU6050 to your ESP32:
   - VCC -> 3.3V
   - GND -> GND
   - SCL -> GPIO 22
   - SDA -> GPIO 21

3. Update the WiFi credentials and WebSocket server address in the ESP32 code

## ESP32 Code Example

```cpp
#include <WiFi.h>
#include <WebSocketsClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <MPU6050_tockn.h>

const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* websocket_server = "your_server_ip";
const int websocket_port = 8080;

WebSocketsClient webSocket;
MPU6050 mpu6050(Wire);

void setup() {
  Serial.begin(115200);
  Wire.begin();
  mpu6050.begin();
  mpu6050.calcGyroOffsets(true);
  
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  webSocket.begin(websocket_server, websocket_port, "/esp32");
  webSocket.onEvent(webSocketEvent);
  webSocket.setReconnectInterval(5000);
}

void loop() {
  webSocket.loop();
  
  if (webSocket.isConnected()) {
    mpu6050.update();
    
    StaticJsonDocument<200> doc;
    doc["x"] = mpu6050.getGyroX();
    doc["y"] = mpu6050.getGyroY();
    doc["z"] = mpu6050.getGyroZ();
    
    String jsonString;
    serializeJson(doc, jsonString);
    webSocket.sendTXT(jsonString);
  }
  
  delay(50); // 20Hz update rate
}

void webSocketEvent(WStype_t type, uint8_t * payload, size_t length) {
  switch(type) {
    case WStype_DISCONNECTED:
      Serial.println("Disconnected!");
      break;
    case WStype_CONNECTED:
      Serial.println("Connected!");
      break;
    case WStype_TEXT:
      break;
  }
}
```

## System Architecture

1. ESP32 with MPU6050:
   - Reads gyroscope data
   - Sends data to WebSocket server

2. Node.js WebSocket Server:
   - Receives data from ESP32
   - Processes data for accident detection
   - Broadcasts data to web clients

3. Web Interface:
   - Displays real-time gyroscope data
   - Shows visual alerts for potential accidents
   - Plays audio alerts when accidents are detected

## Accident Detection Logic

The system uses the following criteria to detect potential accidents:
- Sudden spikes in angular velocity (above 250 degrees/second)
- Monitoring changes within a 500ms window

- Combined magnitude of X, Y, Z rotation 


