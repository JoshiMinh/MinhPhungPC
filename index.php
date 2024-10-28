<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinhPhungPC - Build Your PC</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-main">
    <a class="navbar-brand" href="index.php" style="width: 10%;">
        <img src="logo.png" alt="MinhPhungPC Logo" style="width: 100%;">
    </a>
    <div class="mx-auto" style="width: 50%;">
        <div class="search-input-container d-flex">
            <input class="search-input flex-grow-1" type="text" placeholder="Find component" aria-label="Search">
            <button class="search-button" aria-label="Search">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="d-flex align-items-center ml-auto">
        <button id="switchBtn" class="btn btn-link p-0 ml-2" aria-label="Toggle dark mode">
            <i class="bi bi-moon icon" style="color: black;"></i>
        </button>
        <button class="btn btn-primary mx-2" type="button">
            <i class="fas fa-user"></i> Account
        </button>
        <button class="btn btn-secondary" type="button">
            <i class="fas fa-shopping-cart"></i> Cart
        </button>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-secondary navbar-gradient">
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav flex-row w-100">
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Motherboard</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">CPU</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">RAM</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Graphics Card</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Storage</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Power Supply</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Cooler</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Case Fan</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Operating System</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Monitor</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Mouse</a></li>
            <li class="nav-item flex-fill"><a class="nav-link" href="#">Keyboard</a></li>
        </ul>
    </div>
</nav>

<main class="container">
    <div class="row">
        <div class="col text-center my-5">
            <h2>Build Your First PC!</h2>
        </div>
    </div>

    <div class="container">
        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Motherboard" data-component-icon="motherboard.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Motherboard</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/motherboard.png" alt="Motherboard" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="CPU" data-component-icon="processor.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>CPU</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/processor.png" alt="CPU" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="RAM" data-component-icon="memory.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>RAM</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/memory.png" alt="RAM" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="GPU" data-component-icon="vga-card.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>GPU</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/vga-card.png" alt="GPU" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Hard Drive" data-component-icon="solid-state-drive.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Hard Drive</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/solid-state-drive.png" alt="Hard Drive" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Power Supply" data-component-icon="power-supply.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Power Supply</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/power-supply.png" alt="Power Supply" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="CPU Cooler" data-component-icon="cpu cooler.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>CPU Cooler</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/cpu cooler.png" alt="CPU Cooler" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Case Fan" data-component-icon="case fan.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Case Fan</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/case.png" alt="Case Fan" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Operating System" data-component-icon="operating-system.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Operating System</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/windows.png" alt="Operating System" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Monitor" data-component-icon="monitor.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Monitor</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/monitor.png" alt="Monitor" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Mouse" data-component-icon="mouse.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Mouse</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/mouse.png" alt="Mouse" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>

        <div class="component-card my-4 rounded p-3 shadow-sm bg-white text-dark" data-component-name="Keyboard" data-component-icon="keyboard.png">
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span>Keyboard</span>
                    <div class="rounded p-2 d-flex align-items-center mx-2" style="width: 48px; background-color: #f8f9fa;">
                        <img src="Component Icons/keyboard.png" alt="Keyboard" width="32">
                    </div>
                    <span class="text-muted">Please select a component</span>
                </div>
                <button class="btn btn-primary px-4">Select</button>
            </div>
        </div>
    </div>
</main>

<footer class="footer-gradient py-4 text-white">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>
                    <strong>Email:</strong> <a href="mailto:binhangia241273@gmail.com" class="text-white">binhangia241273@gmail.com</a><br>
                    <strong>Phone:</strong> <a href="tel:+84907067721" class="text-white">0907067721</a>
                </p>
            </div>
            <div class="col-md-4">
                <h5>About Us</h5>
                <p>At MinhPhungPC, we help you build the perfect PC tailored to your needs. Whether for gaming, design, or everyday use, our expert recommendations ensure you get the best performance.</p>
            </div>
            <div class="col-md-4">
                <h5>See on GitHub</h5>
                <a href="https://github.com/JoshiMinh/MinhPhungPC" target="_blank" class="text-white mx-2" style="font-size: 2rem;">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
        <div class="mt-3">
            <p>&copy; 2024 MinhPhungPC. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="darkmode.js"></script>
</body>
</html>