## Hybrid Email Phishing Detection Using Rule-Based and Random Forest

A hybrid phishing email detection system that integrates rule-based analysis and machine learning to improve phishing detection accuracy. This system is designed to detect sophisticated phishing emails by combining deterministic rules with a trained Random Forest classification model.

## Project Overview
This project proposes a hybrid detection approach that combines:
- Rule-based detection (keyword patterns, suspicious links, header anomalies)
- Machine Learning classification (Random Forest model)
- Gmail API integration for real-world email testing
- Asynchronous processing using queue workers

## Objectives
- Detect phishing emails with higher accuracy using hybrid techniques
- Reduce false positives compared to pure rule-based filtering
- Integrate real email data via Gmail API

## System Architecture
1. Laravel Web Application
2. Queue Worker
3. Machine Learning API (Flask) - Random Forest model is not included in this repository
4. Database
5. Redis (optional - for queue management,faster than database queue)

## Technology Stack
1. PHP (Laravel Framework)
2. Python (Flask API)
3. Scikit-learn
4. Pandas
5. NumPy
6. Database (mySQL)
7. Redis (optional)

## Software Version
- PHP: 8.3.16
- Node Package Manager: 10.9.2
- Laravel: 12.50.0