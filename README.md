**Overview**
This is my final project thesis, where I developed a Mini-Distributed Control System (Mini-DCS) for controlling the 
fermentation temperature of tape ketan (glutinous rice fermentation). The system operates in both automatic and manual modes, 
with a reference temperature range of 35-40°C.

**Features**

✅ Human Machine Interface (HMI) using XAMPP

✅ Automatic Mode (PID control with adjustable Setpoint, Kp, Ki, Kd)

✅ Manual Mode (User manually sets the PWM for system input control)

✅ Data communication using PHP

**System Architecture**

Hardware: ESP32, DHT22 sensor, AC dimmer

Software: PHP, MySQL (XAMPP), HTML/CSS for UI

Control Method: PID algorithm for automatic temperature control

Communication: Data exchange between ESP32 and XAMPP via PHP

**Modes of Operation**

**1. Automatic Mode (PID Control)**

The system calculates the PWM output based on PID control.
User must input Setpoint, Kp, Ki, Kd via the HMI.
The system automatically adjusts heating to maintain 35-40°C.

**2. Manual Mode**

User directly inputs the PWM value to control the heating system.
The system does not perform automatic PID calculations.

**Data Flow & Communication**

1.ESP32 sends sensor data (temperature) to the MySQL database via PHP.

2.The system reads control parameters (Setpoint, Kp, Ki, Kd, or PWM) from the database.

3.The ESP32 processes the data and adjusts the heating system accordingly.

**Future Improvements**

🔹 Enhancing UI/UX for the HMI

🔹 Adding real-time data visualization

🔹 Improving system efficiency & response time
