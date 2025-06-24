<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netutilx API Documentation</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS for theme and layout -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .sidebar {
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #555;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #0d6efd;
            background-color: #e9ecef;
            border-left: 3px solid #0d6efd;
        }
        
        .endpoint-section {
            padding-top: 80px;
            margin-top: -80px;
        }

        .method {
            font-size: 0.9rem;
            font-weight: 700;
            padding: 0.3rem 0.6rem;
            border-radius: 0.25rem;
            color: #fff;
        }
        .method-get { background-color: #0d6efd; }

        .endpoint-path {
            font-family: 'Courier New', Courier, monospace;
            background-color: #e9ecef;
            padding: 0.5rem 0.8rem;
            border-radius: 0.25rem;
            font-weight: 600;
            color: #333;
        }

        pre {
            background-color: #212529;
            color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: #495057;
            border: none;
            color: #fff;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        pre:hover .copy-btn {
            opacity: 1;
        }
        
        .copy-btn .bi-clipboard-check-fill { display: none; }
        .copy-btn.copied .bi-clipboard { display: none; }
        .copy-btn.copied .bi-clipboard-check-fill { display: inline-block; }


        h1, h2, h3 {
            font-weight: 600;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.05);
        }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#sidebar-nav" data-bs-offset="100">

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">🚀 Netutilx API Documentation</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="mailto:arghadasofficial@gmail.com">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 d-none d-lg-block">
                <nav id="sidebar-nav" class="sidebar nav flex-column">
                    <a class="nav-link" href="#introduction">Introduction</a>
                    <a class="nav-link" href="#get-dns-servers">Get DNS Servers</a>
                    <a class="nav-link" href="#get-dns-types">Get DNS Types</a>
                    <a class="nav-link" href="#get-dns-info">Perform DNS Lookup</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <section id="introduction" class="endpoint-section mb-5">
                    <div class="p-4 mb-4 bg-light rounded-3">
                        <h1>Netutilx API</h1>
                        <p class="lead">The powerhouse driving the Netutilx Project, acting as the bridge between the Netutilx Android App, Desktop App and Website. It handles requests & processes data for top-tier network utility performance.</p>
                    </div>

                    <!-- Early Access Section -->
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill flex-shrink-0 me-3" style="font-size: 1.5rem;"></i>
                        <div>
                           <h5 class="alert-heading">Early Access API</h5>
                           This API is currently in early access. For inquiries and to request an API key, please email us at <a href="mailto:arghadasofficial@gmail.com" class="alert-link">arghadasofficial@gmail.com</a>.
                        </div>
                    </div>
                </section>

                <!-- Endpoint: Get DNS Servers -->
                <section id="get-dns-servers" class="endpoint-section mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h2>Get DNS Servers</h2>
                            <p>Retrieves a list of all configured DNS servers available for queries.</p>
                            <div class="d-flex align-items-center mb-3">
                                <span class="method method-get me-3">GET</span>
                                <span class="endpoint-path">/get_dns_servers.php</span>
                            </div>
                            
                            <h5 class="mt-4">Example Response</h5>
                            <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "DNS servers retrieved successfully.",
    "data": [
        {
            "id": "3",
            "name": "Cloudflare",
            "ip_address": "1.1.1.1",
            "created_at": "2025-06-24 16:03:25"
        },
        {
            "id": "1",
            "name": "Google",
            "ip_address": "8.8.8.8",
            "created_at": "2025-06-24 16:03:25"
        },
        {
            "id": "5",
            "name": "OpenDNS",
            "ip_address": "208.67.222.222",
            "created_at": "2025-06-24 16:03:25"
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Endpoint: Get DNS Types -->
                <section id="get-dns-types" class="endpoint-section mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h2>Get DNS Types</h2>
                            <p>Retrieves a list of all supported DNS query types.</p>
                            <div class="d-flex align-items-center mb-3">
                                <span class="method method-get me-3">GET</span>
                                <span class="endpoint-path">/get_dns_types.php</span>
                            </div>
                            <h5 class="mt-4">Example Response</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "DNS types retrieved successfully.",
    "data": [
        {
            "id": "1",
            "name": "A",
            "description": "Address Mapping record",
            "created_at": "2025-06-24 20:18:27"
        },
        {
            "id": "3",
            "name": "MX",
            "description": "Mail Exchange record",
            "created_at": "2025-06-24 20:18:27"
        },
        {
            "id": "2",
            "name": "NS",
            "description": "Name Server record",
            "created_at": "2025-06-24 20:18:27"
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Endpoint: Perform DNS Lookup -->
                <section id="get-dns-info" class="endpoint-section mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h2>Perform DNS Lookup</h2>
                            <p>Performs a live DNS lookup based on the provided query, server, and type. This is the main query endpoint.</p>
                            <div class="d-flex align-items-center mb-3">
                                <span class="method method-get me-3">GET</span>
                                <span class="endpoint-path">/get_dns_info.php</span>
                            </div>
                            
                            <h5 class="mt-4">Query Parameters</h5>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>query</code></td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>The domain name (e.g., <code>google.com</code>) or IP address to look up.</td>
                                    </tr>
                                    <tr>
                                        <td><code>serverId</code></td>
                                        <td>integer</td>
                                        <td>Yes</td>
                                        <td>The <code>id</code> of the DNS server to use for the query.</td>
                                    </tr>
                                    <tr>
                                        <td><code>typeId</code></td>
                                        <td>integer</td>
                                        <td>Yes</td>
                                        <td>The <code>id</code> of the DNS record type to query.</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <h5 class="mt-4">Example Success Response (A Record)</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "Dns Info Fetched Successfully",
    "data": [
        {
            "name": "google.com.",
            "ttl": 300,
            "class": "IN",
            "type": "A",
            "data": "142.251.220.78"
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>

                            <h5 class="mt-4">Example Success Response (NS Record)</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "Dns Info Fetched Successfully",
    "data": [
        {
            "name": "google.com.",
            "ttl": 20096,
            "class": "IN",
            "type": "NS",
            "data": "ns3.google.com."
        },
        {
            "name": "google.com.",
            "ttl": 20096,
            "class": "IN",
            "type": "NS",
            "data": "ns1.google.com."
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>

                            <h5 class="mt-4">Example Success Response (MX Record)</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "Dns Info Fetched Successfully",
    "data": [
        {
            "name": "google.com.",
            "ttl": 18,
            "class": "IN",
            "type": "MX",
            "data": "10 smtp.google.com."
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>
                            
                            <h5 class="mt-4">Example Success Response (SOA Record)</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "Dns Info Fetched Successfully",
    "data": [
        {
            "name": "google.com.",
            "ttl": 6,
            "class": "IN",
            "type": "SOA",
            "data": {
                "mname": "ns1.google.com.",
                "rname": "dns-admin.google.com.",
                "serial": "774702615",
                "refresh": 900,
                "retry": 900,
                "expire": 1800,
                "minimum": 60
            }
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>

                            <h5 class="mt-4">Example Success Response (TXT Record)</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "Dns Info Fetched Successfully",
    "data": [
        {
            "name": "google.com.",
            "ttl": 2623,
            "class": "IN",
            "type": "TXT",
            "data": "v=spf1 include:_spf.google.com ~all"
        },
        {
            "name": "google.com.",
            "ttl": 2623,
            "class": "IN",
            "type": "TXT",
            "data": "google-site-verification=wD8N7i1JTNTkezJ49swvWW48f8_9xveREV4oB-0Hf5o"
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>

                            <h5 class="mt-4">Example Success Response (PTR Record)</h5>
                             <div class="position-relative">
                                <pre><code>{
    "success": true,
    "message": "Dns Info Fetched Successfully",
    "data": [
        {
            "name": "78.220.251.142.in-addr.arpa.",
            "ttl": 2935,
            "class": "IN",
            "type": "PTR",
            "data": "pnbomb-bd-in-f14.1e100.net."
        }
    ]
}</code></pre>
                                <button class="copy-btn"><i class="bi bi-clipboard"></i><i class="bi bi-clipboard-check-fill"></i></button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
    <footer class="text-center py-4 mt-5 bg-dark text-white">
        <p class="mb-0">&copy; 2025 Netutilx Project. For queries, contact <a href="mailto:arghadasofficial@gmail.com" class="text-white">arghadasofficial@gmail.com</a>.</p>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript for interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Copy to clipboard functionality
            const copyButtons = document.querySelectorAll('.copy-btn');
            copyButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const code = btn.previousElementSibling.textContent;
                    // Using a temporary textarea to copy text
                    const textArea = document.createElement('textarea');
                    textArea.value = code;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        btn.classList.add('copied');
                        setTimeout(() => {
                            btn.classList.remove('copied');
                        }, 2000);
                    } catch (err) {
                        console.error('Failed to copy text: ', err);
                    }
                    document.body.removeChild(textArea);
                });
            });
        });
    </script>
</body>
</html>
