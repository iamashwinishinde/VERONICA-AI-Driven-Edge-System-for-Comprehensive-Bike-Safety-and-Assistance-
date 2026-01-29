#Preview
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()
![Uploading image.png…]()









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
