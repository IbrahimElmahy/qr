@echo off
echo ========================================
echo    إنشاء مشروع أندرويد نظيف
echo ========================================

echo.
echo 1. إنشاء مجلد جديد للمشروع...
echo.

set PROJECT_NAME=shipment_tracking_app
set PROJECT_DIR=%PROJECT_NAME%

if exist "%PROJECT_DIR%" (
    echo حذف المجلد القديم...
    rmdir /s /q "%PROJECT_DIR%"
)

mkdir "%PROJECT_DIR%"
cd "%PROJECT_DIR%"

echo.
echo 2. إنشاء هيكل المشروع...
echo.

mkdir app
mkdir app\src
mkdir app\src\main
mkdir app\src\main\java
mkdir app\src\main\java\com
mkdir app\src\main\java\com\example
mkdir app\src\main\java\com\example\shipmenttracking
mkdir app\src\main\res
mkdir app\src\main\res\layout
mkdir app\src\main\res\values
mkdir app\src\main\res\drawable
mkdir app\src\main\res\mipmap-hdpi
mkdir app\src\main\res\mipmap-mdpi
mkdir app\src\main\res\mipmap-xhdpi
mkdir app\src\main\res\mipmap-xxhdpi
mkdir app\src\main\res\mipmap-xxxhdpi
mkdir app\src\main\res\values-night
mkdir app\src\main\res\mipmap-anydpi-v26
mkdir app\src\main\res\xml
mkdir app\src\main\res\menu
mkdir app\src\main\res\navigation
mkdir app\src\test
mkdir app\src\test\java
mkdir app\src\androidTest
mkdir app\src\androidTest\java
mkdir gradle
mkdir gradle\wrapper

echo.
echo 3. إنشاء ملفات المشروع الأساسية...
echo.

echo // Top-level build file where you can add configuration options common to all sub-projects/modules. > build.gradle.kts
echo plugins { >> build.gradle.kts
echo     id("com.android.application") version "8.1.4" apply false >> build.gradle.kts
echo     id("org.jetbrains.kotlin.android") version "1.9.10" apply false >> build.gradle.kts
echo     id("org.jetbrains.kotlin.kapt") version "1.9.10" apply false >> build.gradle.kts
echo } >> build.gradle.kts

echo.
echo 4. إنشاء ملف app/build.gradle.kts...
echo.

echo plugins { > app\build.gradle.kts
echo     id("com.android.application") >> app\build.gradle.kts
echo     id("org.jetbrains.kotlin.android") >> app\build.gradle.kts
echo     id("org.jetbrains.kotlin.kapt") >> app\build.gradle.kts
echo } >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo android { >> app\build.gradle.kts
echo     namespace = "com.example.shipmenttracking" >> app\build.gradle.kts
echo     compileSdk = 34 >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     defaultConfig { >> app\build.gradle.kts
echo         applicationId = "com.example.shipmenttracking" >> app\build.gradle.kts
echo         minSdk = 24 >> app\build.gradle.kts
echo         targetSdk = 34 >> app\build.gradle.kts
echo         versionCode = 1 >> app\build.gradle.kts
echo         versionName = "1.0" >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo         testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner" >> app\build.gradle.kts
echo     } >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     buildTypes { >> app\build.gradle.kts
echo         release { >> app\build.gradle.kts
echo             isMinifyEnabled = false >> app\build.gradle.kts
echo             proguardFiles( >> app\build.gradle.kts
echo                 getDefaultProguardFile("proguard-android-optimize.txt"), >> app\build.gradle.kts
echo                 "proguard-rules.pro" >> app\build.gradle.kts
echo             ) >> app\build.gradle.kts
echo         } >> app\build.gradle.kts
echo     } >> app\build.gradle.kts
echo     compileOptions { >> app\build.gradle.kts
echo         sourceCompatibility = JavaVersion.VERSION_17 >> app\build.gradle.kts
echo         targetCompatibility = JavaVersion.VERSION_17 >> app\build.gradle.kts
echo     } >> app\build.gradle.kts
echo     kotlinOptions { >> app\build.gradle.kts
echo         jvmTarget = "17" >> app\build.gradle.kts
echo     } >> app\build.gradle.kts
echo     buildFeatures { >> app\build.gradle.kts
echo         viewBinding = true >> app\build.gradle.kts
echo     } >> app\build.gradle.kts
echo } >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo dependencies { >> app\build.gradle.kts
echo     // Core Android libraries >> app\build.gradle.kts
echo     implementation("androidx.core:core-ktx:1.12.0") >> app\build.gradle.kts
echo     implementation("androidx.appcompat:appcompat:1.6.1") >> app\build.gradle.kts
echo     implementation("com.google.android.material:material:1.10.0") >> app\build.gradle.kts
echo     implementation("androidx.constraintlayout:constraintlayout:2.1.4") >> app\build.gradle.kts
echo     implementation("androidx.activity:activity-ktx:1.8.0") >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     // Navigation component >> app\build.gradle.kts
echo     implementation("androidx.navigation:navigation-fragment-ktx:2.7.5") >> app\build.gradle.kts
echo     implementation("androidx.navigation:navigation-ui-ktx:2.7.5") >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     // ViewModel and LiveData >> app\build.gradle.kts
echo     implementation("androidx.lifecycle:lifecycle-viewmodel-ktx:2.7.0") >> app\build.gradle.kts
echo     implementation("androidx.lifecycle:lifecycle-livedata-ktx:2.7.0") >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     // Retrofit and Gson >> app\build.gradle.kts
echo     implementation("com.squareup.retrofit2:retrofit:2.9.0") >> app\build.gradle.kts
echo     implementation("com.squareup.retrofit2:converter-gson:2.9.0") >> app\build.gradle.kts
echo     implementation("com.squareup.okhttp3:logging-interceptor:4.11.0") >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     // Barcode scanner >> app\build.gradle.kts
echo     implementation("com.journeyapps:zxing-android-embedded:4.3.0") >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     // Room >> app\build.gradle.kts
echo     implementation("androidx.room:room-runtime:2.6.1") >> app\build.gradle.kts
echo     implementation("androidx.room:room-ktx:2.6.1") >> app\build.gradle.kts
echo     kapt("androidx.room:room-compiler:2.6.1") >> app\build.gradle.kts
echo. >> app\build.gradle.kts
echo     // Testing libraries >> app\build.gradle.kts
echo     testImplementation("junit:junit:4.13.2") >> app\build.gradle.kts
echo     androidTestImplementation("androidx.test.ext:junit:1.1.5") >> app\build.gradle.kts
echo     androidTestImplementation("androidx.test.espresso:espresso-core:3.5.1") >> app\build.gradle.kts
echo } >> app\build.gradle.kts

echo.
echo 5. إنشاء ملفات إضافية...
echo.

echo # Project-wide Gradle settings. > gradle.properties
echo org.gradle.jvmargs=-Xmx4096m -Dfile.encoding=UTF-8 >> gradle.properties
echo org.gradle.parallel=true >> gradle.properties
echo org.gradle.caching=true >> gradle.properties
echo org.gradle.configureondemand=true >> gradle.properties
echo android.useAndroidX=true >> gradle.properties
echo kotlin.code.style=official >> gradle.properties
echo android.nonTransitiveRClass=true >> gradle.properties

echo.
echo 6. إنشاء ملفات Gradle Wrapper...
echo.

echo #Mon Dec 25 10:00:00 UTC 2023 > gradle\wrapper\gradle-wrapper.properties
echo distributionBase=GRADLE_USER_HOME >> gradle\wrapper\gradle-wrapper.properties
echo distributionPath=wrapper/dists >> gradle\wrapper\gradle-wrapper.properties
echo distributionUrl=https\://services.gradle.org/distributions/gradle-8.4-bin.zip >> gradle\wrapper\gradle-wrapper.properties
echo zipStoreBase=GRADLE_USER_HOME >> gradle\wrapper\gradle-wrapper.properties
echo zipStorePath=wrapper/dists >> gradle\wrapper\gradle-wrapper.properties

echo.
echo 7. إنشاء ملفات Android الأساسية...
echo.

echo ^<?xml version="1.0" encoding="utf-8"?^? > app\src\main\AndroidManifest.xml
echo ^<manifest xmlns:android="http://schemas.android.com/apk/res/android" >> app\src\main\AndroidManifest.xml
echo     xmlns:tools="http://schemas.android.com/tools"^> >> app\src\main\AndroidManifest.xml
echo. >> app\src\main\AndroidManifest.xml
echo     ^<uses-permission android:name="android.permission.INTERNET" /^> >> app\src\main\AndroidManifest.xml
echo     ^<uses-permission android:name="android.permission.CAMERA" /^> >> app\src\main\AndroidManifest.xml
echo. >> app\src\main\AndroidManifest.xml
echo     ^<application >> app\src\main\AndroidManifest.xml
echo         android:allowBackup="true" >> app\src\main\AndroidManifest.xml
echo         android:dataExtractionRules="@xml/data_extraction_rules" >> app\src\main\AndroidManifest.xml
echo         android:fullBackupContent="@xml/backup_rules" >> app\src\main\AndroidManifest.xml
echo         android:icon="@mipmap/ic_launcher" >> app\src\main\AndroidManifest.xml
echo         android:label="@string/app_name" >> app\src\main\AndroidManifest.xml
echo         android:roundIcon="@mipmap/ic_launcher_round" >> app\src\main\AndroidManifest.xml
echo         android:supportsRtl="true" >> app\src\main\AndroidManifest.xml
echo         android:theme="@style/Theme.ShipmentTracking" >> app\src\main\AndroidManifest.xml
echo         android:usesCleartextTraffic="true" >> app\src\main\AndroidManifest.xml
echo         tools:targetApi="31"^> >> app\src\main\AndroidManifest.xml
echo. >> app\src\main\AndroidManifest.xml
echo         ^<activity >> app\src\main\AndroidManifest.xml
echo             android:name=".MainActivity" >> app\src\main\AndroidManifest.xml
echo             android:exported="true" >> app\src\main\AndroidManifest.xml
echo             android:theme="@style/Theme.ShipmentTracking"^> >> app\src\main\AndroidManifest.xml
echo             ^<intent-filter^> >> app\src\main\AndroidManifest.xml
echo                 ^<action android:name="android.intent.action.MAIN" /^> >> app\src\main\AndroidManifest.xml
echo                 ^<category android:name="android.intent.category.LAUNCHER" /^> >> app\src\main\AndroidManifest.xml
echo             ^</intent-filter^> >> app\src\main\AndroidManifest.xml
echo         ^</activity^> >> app\src\main\AndroidManifest.xml
echo. >> app\src\main\AndroidManifest.xml
echo     ^</application^> >> app\src\main\AndroidManifest.xml
echo. >> app\src\main\AndroidManifest.xml
echo ^</manifest^> >> app\src\main\AndroidManifest.xml

echo.
echo 8. إنشاء ملفات الموارد...
echo.

echo ^<?xml version="1.0" encoding="utf-8"?^? > app\src\main\res\values\strings.xml
echo ^<resources^> >> app\src\main\res\values\strings.xml
echo     ^<string name="app_name"^>Shipment Tracking^</string^> >> app\src\main\res\values\strings.xml
echo ^</resources^> >> app\src\main\res\values\strings.xml

echo.
echo 9. إنشاء ملف MainActivity...
echo.

echo package com.example.shipmenttracking > app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo. >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo import android.os.Bundle >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo import androidx.appcompat.app.AppCompatActivity >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo. >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo class MainActivity : AppCompatActivity() { >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo     override fun onCreate(savedInstanceState: Bundle?) { >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo         super.onCreate(savedInstanceState) >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo         setContentView(R.layout.activity_main) >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo     } >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt
echo } >> app\src\main\java\com\example\shipmenttracking\MainActivity.kt

echo.
echo 10. إنشاء ملف activity_main.xml...
echo.

echo ^<?xml version="1.0" encoding="utf-8"?^? > app\src\main\res\layout\activity_main.xml
echo ^<androidx.constraintlayout.widget.ConstraintLayout xmlns:android="http://schemas.android.com/apk/res/android" >> app\src\main\res\layout\activity_main.xml
echo     xmlns:app="http://schemas.android.com/apk/res-auto" >> app\src\main\res\layout\activity_main.xml
echo     xmlns:tools="http://schemas.android.com/tools" >> app\src\main\res\layout\activity_main.xml
echo     android:layout_width="match_parent" >> app\src\main\res\layout\activity_main.xml
echo     android:layout_height="match_parent" >> app\src\main\res\layout\activity_main.xml
echo     tools:context=".MainActivity"^> >> app\src\main\res\layout\activity_main.xml
echo. >> app\src\main\res\layout\activity_main.xml
echo     ^<TextView >> app\src\main\res\layout\activity_main.xml
echo         android:layout_width="wrap_content" >> app\src\main\res\layout\activity_main.xml
echo         android:layout_height="wrap_content" >> app\src\main\res\layout\activity_main.xml
echo         android:text="Hello World!" >> app\src\main\res\layout\activity_main.xml
echo         app:layout_constraintBottom_toBottomOf="parent" >> app\src\main\res\layout\activity_main.xml
echo         app:layout_constraintEnd_toEndOf="parent" >> app\src\main\res\layout\activity_main.xml
echo         app:layout_constraintStart_toStartOf="parent" >> app\src\main\res\layout\activity_main.xml
echo         app:layout_constraintTop_toTopOf="parent" /^> >> app\src\main\res\layout\activity_main.xml
echo. >> app\src\main\res\layout\activity_main.xml
echo ^</androidx.constraintlayout.widget.ConstraintLayout^> >> app\src\main\res\layout\activity_main.xml

echo.
echo 11. إنشاء ملفات Gradle Wrapper...
echo.

echo @echo off > gradlew.bat
echo @rem >> gradlew.bat
echo @rem Copyright 2015 the original author or authors. >> gradlew.bat
echo @rem >> gradlew.bat
echo @rem Licensed under the Apache License, Version 2.0 (the "License"); >> gradlew.bat
echo @rem you may not use this file except in compliance with the License. >> gradlew.bat
echo @rem You may obtain a copy of the License at >> gradlew.bat
echo @rem >> gradlew.bat
echo @rem      https://www.apache.org/licenses/LICENSE-2.0 >> gradlew.bat
echo @rem >> gradlew.bat
echo @rem Unless required by applicable law or agreed to in writing, software >> gradlew.bat
echo @rem distributed under the License is distributed on an "AS IS" BASIS, >> gradlew.bat
echo @rem WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. >> gradlew.bat
echo @rem See the License for the specific language governing permissions and >> gradlew.bat
echo @rem limitations under the License. >> gradlew.bat
echo @rem >> gradlew.bat
echo. >> gradlew.bat
echo @rem This file has been generated by Gradle. >> gradlew.bat
echo @rem >> gradlew.bat
echo @rem When you run Gradle with an init script, it will not be able to >> gradlew.bat
echo @rem find the root project directory. This is because the init script >> gradlew.bat
echo @rem is run in the context of the Gradle build, not the Gradle wrapper. >> gradlew.bat
echo @rem >> gradlew.bat
echo @rem This script will find the root project directory and run the >> gradlew.bat
echo @rem init script from there. >> gradlew.bat
echo @rem >> gradlew.bat
echo. >> gradlew.bat
echo set DIRNAME=%%~dp0 >> gradlew.bat
echo if "%%DIRNAME%%" == "" set DIRNAME=. >> gradlew.bat
echo set APP_BASE_NAME=%%~n0 >> gradlew.bat
echo set APP_HOME=%%DIRNAME%% >> gradlew.bat
echo. >> gradlew.bat
echo @rem Add default JVM options here. You can also use JAVA_OPTS and GRADLE_OPTS to pass JVM options to this script. >> gradlew.bat
echo set DEFAULT_JVM_OPTS="-Xmx64m" "-Xms64m" >> gradlew.bat
echo. >> gradlew.bat
echo @rem Find java.exe >> gradlew.bat
echo if defined JAVA_HOME goto findJavaFromJavaHome >> gradlew.bat
echo. >> gradlew.bat
echo set JAVA_EXE=java.exe >> gradlew.bat
echo %JAVA_EXE% -version >NUL 2>&1 >> gradlew.bat
echo if "%%ERRORLEVEL%%" == "0" goto execute >> gradlew.bat
echo. >> gradlew.bat
echo echo. >> gradlew.bat
echo echo ERROR: JAVA_HOME is not set and no 'java' command could be found in your PATH. >> gradlew.bat
echo echo. >> gradlew.bat
echo echo Please set the JAVA_HOME variable in your environment to match the >> gradlew.bat
echo echo location of your Java installation. >> gradlew.bat
echo. >> gradlew.bat
echo goto fail >> gradlew.bat
echo. >> gradlew.bat
echo :findJavaFromJavaHome >> gradlew.bat
echo set JAVA_HOME=%%JAVA_HOME:"=%% >> gradlew.bat
echo set JAVA_EXE=%%JAVA_HOME%%\bin\java.exe >> gradlew.bat
echo. >> gradlew.bat
echo if exist "%%JAVA_EXE%%" goto execute >> gradlew.bat
echo. >> gradlew.bat
echo echo. >> gradlew.bat
echo echo ERROR: JAVA_HOME is set to an invalid directory: %%JAVA_HOME%% >> gradlew.bat
echo echo. >> gradlew.bat
echo echo Please set the JAVA_HOME variable in your environment to match the >> gradlew.bat
echo echo location of your Java installation. >> gradlew.bat
echo. >> gradlew.bat
echo goto fail >> gradlew.bat
echo. >> gradlew.bat
echo :execute >> gradlew.bat
echo @rem Setup the command line >> gradlew.bat
echo. >> gradlew.bat
echo set CLASSPATH=%%APP_HOME%%\gradle\wrapper\gradle-wrapper.jar >> gradlew.bat
echo. >> gradlew.bat
echo @rem Execute Gradle >> gradlew.bat
echo "%%JAVA_EXE%%" %%DEFAULT_JVM_OPTS%% %%JAVA_OPTS%% %%GRADLE_OPTS%% "-Dorg.gradle.appname=%%APP_BASE_NAME%%" -classpath "%%CLASSPATH%%" org.gradle.wrapper.GradleWrapperMain %%* >> gradlew.bat
echo. >> gradlew.bat
echo :end >> gradlew.bat
echo @rem End local scope for the variables with windows NT shell >> gradlew.bat
echo if "%%ERRORLEVEL%%"=="0" goto mainEnd >> gradlew.bat
echo. >> gradlew.bat
echo :fail >> gradlew.bat
echo rem Set variable GRADLE_EXIT_CONSOLE if you need the _script_ return code instead of >> gradlew.bat
echo rem the _cmd_ return code. >> gradlew.bat
echo if not "" == "%%GRADLE_EXIT_CONSOLE%%" exit 1 >> gradlew.bat
echo exit /b 1 >> gradlew.bat
echo. >> gradlew.bat
echo :mainEnd >> gradlew.bat
echo if "%%OS%%"=="Windows_NT" endlocal >> gradlew.bat
echo. >> gradlew.bat
echo :omega >> gradlew.bat

echo.
echo 12. إنشاء ملف settings.gradle.kts...
echo.

echo rootProject.name = "shipment_tracking_app" > settings.gradle.kts
echo include(":app") >> settings.gradle.kts

echo.
echo 13. إنشاء ملف proguard-rules.pro...
echo.

echo # Add project specific ProGuard rules here. > app\proguard-rules.pro
echo # You can control the set of applied configuration files using the >> app\proguard-rules.pro
echo # proguardFiles setting in build.gradle. >> app\proguard-rules.pro
echo # >> app\proguard-rules.pro
echo # For more details, see >> app\proguard-rules.pro
echo #   http://developer.android.com/guide/developing/tools/proguard.html >> app\proguard-rules.pro
echo # >> app\proguard-rules.pro
echo # If your project uses WebView with JS, uncomment the following >> app\proguard-rules.pro
echo # and specify the fully qualified class name to the JavaScript interface >> app\proguard-rules.pro
echo # class: >> app\proguard-rules.pro
echo #-keepclassmembers class fqcn.of.javascript.interface.for.webview { >> app\proguard-rules.pro
echo #   public *; >> app\proguard-rules.pro
echo #} >> app\proguard-rules.pro

echo.
echo 14. إنشاء ملف README...
echo.

echo # Shipment Tracking App > README.md
echo. >> README.md
echo تطبيق تتبع الشحنات >> README.md
echo. >> README.md
echo ## الميزات >> README.md
echo - إدارة شركات الشحن >> README.md
echo - مسح الباركود >> README.md
echo - تتبع الشحنات >> README.md
echo - الإحصائيات >> README.md
echo. >> README.md
echo ## التثبيت >> README.md
echo 1. افتح المشروع في Android Studio >> README.md
echo 2. انتظر حتى يتم تحميل Gradle >> README.md
echo 3. اضغط Build ^> Build APK >> README.md
echo 4. انسخ ملف APK إلى هاتفك >> README.md
echo 5. ثبت التطبيق >> README.md

echo.
echo 15. إنشاء ملف ZIP للمشروع...
echo.

cd ..
Compress-Archive -Path "%PROJECT_DIR%" -DestinationPath "clean_android_project.zip" -Force

echo.
echo ========================================
echo    تم إنشاء مشروع أندرويد نظيف!
echo ========================================
echo.
echo المشروع الجديد: %PROJECT_DIR%
echo ملف ZIP: clean_android_project.zip
echo.
echo الخطوات التالية:
echo 1. افتح Android Studio
echo 2. File ^> Open
echo 3. اختر مجلد: %PROJECT_DIR%
echo 4. انتظر حتى يتم تحميل Gradle
echo 5. اضغط Build ^> Build APK
echo.
echo أو استخدم ملف ZIP:
echo 1. استخرج clean_android_project.zip
echo 2. افتح المجلد في Android Studio
echo 3. اتبع نفس الخطوات
echo.
pause
