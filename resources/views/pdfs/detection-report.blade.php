<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detection Report - Email Analysis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #0066cc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0066cc;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #0066cc;
            margin-bottom: 10px;
        }
        .email-info {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #0066cc;
        }
        .info-value {
            color: #333;
            word-break: break-all;
        }
        .decision-banner {
            padding: 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        .decision-phishing {
            background-color: #ffcccc;
            border: 2px solid #cc0000;
            color: #990000;
        }
        .decision-legitimate {
            background-color: #ccffcc;
            border: 2px solid #009900;
            color: #009900;
        }
        .detection-results {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .detection-col {
            display: table-cell;
            width: 48%;
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 12px;
        }
        .detection-col:first-child {
            margin-right: 2%;
        }
        .result-box {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 8px;
            border-left: 4px solid #0066cc;
        }
        .status-phishing {
            background-color: #ffe6e6;
            color: #cc0000;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .status-legitimate {
            background-color: #e6ffe6;
            color: #009900;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .status-suspicious {
            background-color: #ffffcc;
            color: #cc6600;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .evidence-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .evidence-table th {
            background-color: #0066cc;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0066cc;
        }
        .evidence-table td {
            padding: 8px;
            border: 1px solid #ddd;
            word-break: break-word;
        }
        .evidence-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .threat-tag {
            display: inline-block;
            background-color: #ffcccc;
            color: #cc0000;
            padding: 2px 5px;
            margin: 2px;
            font-size: 10px;
            border-radius: 2px;
        }
        .safe-tag {
            display: inline-block;
            background-color: #ccffcc;
            color: #009900;
            padding: 2px 5px;
            margin: 2px;
            font-size: 10px;
            border-radius: 2px;
        }
        .unknown-tag {
            display: inline-block;
            background-color: #f0f0f0;
            color: #666;
            padding: 2px 5px;
            margin: 2px;
            font-size: 10px;
            border-radius: 2px;
        }
        .timestamp {
            text-align: right;
            font-size: 10px;
            color: #999;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-evidence {
            background-color: #e6f2ff;
            border: 1px solid #99ccff;
            padding: 10px;
            border-radius: 3px;
            font-size: 12px;
            color: #003366;
        }
        .legitimate-evidence {
            background-color: #e6ffe6;
            border: 1px solid #99ff99;
            padding: 10px;
            border-radius: 3px;
            font-size: 12px;
            color: #003300;
            margin-top: 10px;
        }
        .legitimate-evidence ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        .legitimate-evidence li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📧 Email Phishing Detection Report</h1>
        <p><strong>Report ID:</strong> {{ $result->id }}</p>
        <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Email Information -->
    <div class="section">
        <div class="section-title">Email Information</div>
        <div class="email-info">
            <div class="info-row">
                <span class="info-label">From:</span>
                <span class="info-value">{{ $result->email->from }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Subject:</span>
                <span class="info-value">{{ $result->email->subject }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email Date:</span>
                <span class="info-value">{{ $result->email->date ? $result->email->date->format('d/m/Y H:i:s') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Scanned:</span>
                <span class="info-value">{{ $result->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Message ID:</span>
                <span class="info-value" style="font-family: monospace; font-size: 10px;">{{ $result->message_id }}</span>
            </div>
        </div>
    </div>

    <!-- Final Decision -->
    <div class="section">
        <div class="section-title">Final Detection Result</div>
        <div class="decision-banner {{ $result->final_decision === 'phishing' ? 'decision-phishing' : 'decision-legitimate' }}">
            ⚠️ FINAL DECISION: <span style="text-transform: uppercase;">{{ $result->final_decision }}</span>
        </div>
    </div>

    <!-- Detection Results -->
    <div class="section">
        <div class="section-title">Detection Analysis</div>
        
        <div style="display: table; width: 100%; table-layout: fixed;">
            <!-- Rule-Based Result -->
            <div class="detection-col">
                <div style="font-weight: bold; margin-bottom: 8px;">📋 Rule-Based Detection</div>
                <div class="result-box">
                    <div style="margin-bottom: 8px;">
                        <strong>Status:</strong>
                        <span class="status-{{ strtolower($result->rule_result) }}">{{ ucfirst($result->rule_result) }}</span>
                    </div>
                    <div>
                        <strong>Score:</strong> {{ $result->rule_score ?? 0 }}/100
                    </div>
                </div>
            </div>

            <!-- ML Detection Result -->
            <div class="detection-col">
                <div style="font-weight: bold; margin-bottom: 8px;">🤖 ML-Based Detection</div>
                <div class="result-box">
                    <div style="margin-bottom: 8px;">
                        <strong>Status:</strong>
                        <span class="status-{{ strtolower($result->ml_result) }}">{{ ucfirst($result->ml_result) }}</span>
                    </div>
                    <div>
                        <strong>Confidence:</strong> {{ round($result->ml_confidence * 100) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evidence Report -->
    <div class="section">
        <div class="section-title">Evidence Report</div>

        <!-- URL Evidence -->
        @if($result->urlEvidences->count() > 0)
        <div style="margin-bottom: 15px;">
            <strong style="font-size: 13px;">🔗 URL Analysis ({{ $result->urlEvidences->count() }} URLs checked)</strong>
            <table class="evidence-table">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Explanation</th>
                        <th>Threat Types</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result->urlEvidences as $evidence)
                    <tr>
                        <td style="word-break: break-word;">{{ $evidence->url }}</td>
                        <td>
                            @if($evidence->status === 'malicious')
                                <span class="threat-tag">{{ ucfirst($evidence->status) }}</span>
                            @elseif($evidence->status === 'safe')
                                <span class="safe-tag">{{ ucfirst($evidence->status) }}</span>
                            @else
                                <span class="unknown-tag">{{ ucfirst($evidence->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $evidence->explanation }}</td>
                        <td>
                            @if(!empty($evidence->threat_types))
                                @foreach($evidence->threat_types as $threat)
                                    <span class="threat-tag">{{ $threat }}</span>
                                @endforeach
                            @else
                                <span class="unknown-tag">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Authentication Evidence -->
        @if($result->authenticationEvidences->count() > 0)
        <div style="margin-bottom: 15px;">
            <strong style="font-size: 13px;">🔐 Authentication Analysis (SPF, DKIM, DMARC)</strong>
            <table class="evidence-table">
                <thead>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                        <th>Explanation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result->authenticationEvidences as $evidence)
                    <tr>
                        <td>{{ ucfirst($evidence->check_type) }}</td>
                        <td>
                            @if($evidence->status === 'pass')
                                <span class="safe-tag">PASS</span>
                            @else
                                <span class="threat-tag">{{ strtoupper($evidence->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $evidence->explanation }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Keyword Evidence -->
        @if($result->keywordEvidences->count() > 0)
        <div style="margin-bottom: 15px;">
            <strong style="font-size: 13px;">🔤 Keyword Analysis</strong>
            <table class="evidence-table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Classification</th>
                        <th>Explanation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result->keywordEvidences as $evidence)
                    <tr>
                        <td>{{ $evidence->keyword }}</td>
                        <td>
                            @if($evidence->classification === 'phishing')
                                <span class="threat-tag">PHISHING</span>
                            @else
                                <span class="safe-tag">{{ strtoupper($evidence->classification) }}</span>
                            @endif
                        </td>
                        <td>{{ $evidence->explanation }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- HTML Anomaly Evidence -->
        @if($result->htmlAnomalyEvidences->count() > 0)
        <div style="margin-bottom: 15px;">
            <strong style="font-size: 13px;">⚠️ HTML Anomalies</strong>
            <table class="evidence-table">
                <thead>
                    <tr>
                        <th>Anomaly</th>
                        <th>Severity</th>
                        <th>Explanation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result->htmlAnomalyEvidences as $evidence)
                    <tr>
                        <td>{{ $evidence->anomaly_type }}</td>
                        <td>
                            @if(in_array($evidence->severity, ['critical', 'high']))
                                <span class="threat-tag">{{ strtoupper($evidence->severity) }}</span>
                            @else
                                <span class="unknown-tag">{{ strtoupper($evidence->severity) }}</span>
                            @endif
                        </td>
                        <td>{{ $evidence->explanation }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- No Evidence or Legitimate Conclusion -->
        @if($result->urlEvidences->count() === 0 && 
            $result->authenticationEvidences->count() === 0 && 
            $result->keywordEvidences->count() === 0 && 
            $result->htmlAnomalyEvidences->count() === 0)
        <div class="no-evidence">
            ℹ️ No specific evidence collected for this email.
        </div>
        @else
            @if($result->final_decision === 'legitimate')
            <div class="legitimate-evidence">
                <strong>✅ Why This Email Appears Legitimate</strong>
                <p>The following evidence supports the classification of this email as legitimate:</p>
                <ul>
                    @if($result->authenticationEvidences->where('classification', 'legitimate')->count() > 0)
                        <li>Authentication checks (SPF, DKIM, DMARC) passed successfully</li>
                    @endif
                    @if($result->urlEvidences->where('classification', 'legitimate')->count() > 0)
                        <li>All URLs checked appear safe and trustworthy</li>
                    @endif
                    @if($result->keywordEvidences->where('classification', 'legitimate')->count() === 0)
                        <li>No suspicious phishing keywords detected</li>
                    @endif
                    @if($result->htmlAnomalyEvidences->where('classification', 'legitimate')->count() === 0)
                        <li>No suspicious HTML anomalies found</li>
                    @endif
                </ul>
            </div>
            @endif
        @endif
    </div>

    <!-- Footer -->
    <div class="timestamp">
        Report generated on {{ now()->format('l, F j, Y \a\t H:i:s') }} | PhishingFYP Detection System
    </div>
</body>
</html>
