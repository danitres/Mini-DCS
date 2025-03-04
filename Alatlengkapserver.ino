#include <WiFi.h>
#include <HTTPClient.h>
#include <dimmable_light.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include "DHT.h"
#include <PID_v1.h>

// Konfigurasi WiFi
const char* ssid = "KONTRAKAN 5 BATANG";
const char* password = "Akuikhlas";
const char* serverInsert = "http://192.168.100.12/monitoring/insert_data.php";
const char* serverGetMode = "http://192.168.100.12/monitoring/get_mode.php";

// Konfigurasi Zero-Cross dan Dimmer
#define Z_C_PIN   13
#define DIM_1_PIN 12

// Konfigurasi DHT22
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

// Inisialisasi Dimmer
DimmableLight light1(DIM_1_PIN);

// Inisialisasi LCD I2C
LiquidCrystal_I2C lcd(0x27, 16, 2);

// Variabel PID
double Setpoint, Input, Output;
double Kp = 35.01, Ki = 1.71, Kd = 122.94;
double lastInput = 0;
unsigned long previousMillis = 0;
const long interval = 1000;

// Inisialisasi PID
PID myPID(&Input, &Output, &Setpoint, Kp, Ki, Kd, DIRECT);

// Variabel Mode
bool modeAuto = true;  // Default: AUTO
bool powerOn = true;   // Default: ON
int pwmManual = 0;     // PWM manual

void setup() {
    Serial.begin(115200);

    // Koneksi WiFi
    WiFi.begin(ssid, password);
    Serial.print("Menghubungkan ke WiFi");
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nWiFi Terhubung!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
    
    // Inisialisasi dimmer
    DimmableLight::setSyncPin(Z_C_PIN);
    DimmableLight::begin();
    light1.setBrightness(0);
    dht.begin();

    // Inisialisasi LCD
    lcd.begin();
    lcd.backlight();
    lcd.setCursor(0, 1);

    Setpoint = 40;
    myPID.SetMode(AUTOMATIC);
    myPID.SetSampleTime(interval);
}

void loop() {
    ambilDataDariServer();  // Ambil data di awal loop()

    unsigned long currentMillis = millis();
    if (currentMillis - previousMillis >= interval) {
        previousMillis = currentMillis;

        if (!powerOn) {
            light1.setBrightness(0);
            tampilkanDiLCD(bacaSuhuDHT22(), 0);
            return;
        }

        if (modeAuto) {
            myPID.SetMode(AUTOMATIC);  // Aktifkan PID saat Auto
            Input = bacaSuhuDHT22();
            if (Input == 0) return;

            myPID.Compute();
            int pwmLampu = constrain(Output, 0, 255);
            light1.setBrightness(pwmLampu);

            // Kirim data ke server
            float P = Kp * (Setpoint - Input);
            float I = Ki * (Setpoint - Input) * interval / 1000.0;
            float D = -Kd * (Input - lastInput) / (interval / 1000.0);
            kirimDataKeServer(Input, pwmLampu, P, I, D, Output);

            tampilkanDiLCD(Input, pwmLampu);
            lastInput = Input;
        } else {
            myPID.SetMode(MANUAL);  // Matikan PID saat Manual
            int pwmTerkirim = constrain(pwmManual, 0, 255);
            light1.setBrightness(pwmTerkirim);
            Serial.print("Mengatur PWM Manual: ");
            Serial.println(pwmTerkirim);
            tampilkanDiLCD(bacaSuhuDHT22(), pwmTerkirim);
        }
    }
}

// Fungsi membaca suhu DHT22
double bacaSuhuDHT22() {
    float suhu = dht.readTemperature();
    if (isnan(suhu)) {
        Serial.println("Gagal membaca DHT22!");
        return 0;
    }
    return suhu;
}

// Fungsi mengirim data ke server
void kirimDataKeServer(double suhu, int pwm, double P, double I, double D, double Output) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverInsert);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

        String postData = "temperature=" + String(suhu) + "&pwm=" + String(pwm) +
                          "&P=" + String(P, 2) + "&I=" + String(I, 2) + "&D=" + String(D, 2) + "&Output=" + String(Output, 2);

        Serial.print("Data dikirim: ");
        Serial.println(postData);

        int httpResponseCode = http.POST(postData);

        if (httpResponseCode > 0) {
            Serial.print("Server Response: ");
            Serial.println(http.getString());
        } else {
            Serial.print("Error Mengirim Data: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    } else {
        Serial.println("WiFi tidak terhubung!");
    }
}

// Fungsi menampilkan suhu dan PWM di LCD
void tampilkanDiLCD(double suhu, int pwm) {
    lcd.clear();  // Bersihkan tampilan LCD sebelum menulis ulang

    lcd.setCursor(0, 0);
    lcd.print("Suhu: ");
    lcd.print(suhu, 1);  // Satu angka di belakang koma
    lcd.print(" C");    

    lcd.setCursor(0, 1);
    lcd.print("PWM: ");
    lcd.print(pwm);
    lcd.print("   "); // Tambah spasi agar sisa teks lama terhapus
}


// Fungsi mengambil data mode dan status dari server
void ambilDataDariServer() {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverGetMode);
        int httpResponseCode = http.GET();

        if (httpResponseCode > 0) {
            String response = http.getString();
            Serial.print("Response dari Server: ");
            Serial.println(response);

            // Parsing JSON manual
            int modeIndex = response.indexOf("\"mode\":\"");
            int statusIndex = response.indexOf("\"status\":\"");
            int pwmIndex = response.indexOf("\"pwm\":\""); // Perhatikan perbedaan di sini

            if (modeIndex != -1 && statusIndex != -1 && pwmIndex != -1) {
                String modeValue = response.substring(modeIndex + 8, response.indexOf("\"", modeIndex + 8));
                String statusValue = response.substring(statusIndex + 10, response.indexOf("\"", statusIndex + 10));
                
                // Ambil nilai PWM dengan benar, hilangkan tanda kutip sebelum konversi
                String pwmString = response.substring(pwmIndex + 7, response.indexOf("\"", pwmIndex + 7));
                int pwmValue = pwmString.toInt(); // Sekarang ini akan berhasil

                modeAuto = (modeValue == "auto");
                powerOn = (statusValue == "on");
                pwmManual = pwmValue;

                Serial.print("Mode: "); Serial.println(modeAuto ? "Auto" : "Manual");
                Serial.print("Status: "); Serial.println(powerOn ? "On" : "Off");
                Serial.print("PWM: "); Serial.println(pwmManual);
            }
        } else {
            Serial.print("Gagal mengambil data, Kode HTTP: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    }
}
