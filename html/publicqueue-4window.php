<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/public_queue.css">
    <link rel="stylesheet" href="../css/public_queue_4wd.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script type="text/javascript" src="scripts.js"></script>
    <title>NU Queuest: Public View</title>
</head>
<body>
    <div class="on-break-notif">
        <div class="break-container">
            <h1>BE RIGHT BACK</h1>
            <hr class="break-br">
            <p class="service">Service will resume at</p>
            <p class="service-resume">1:00 PM</p>
        </div>
    </div>
    
    <!-- for the sake of development, values will be explicitly stated in code; this base can be changed once backend has been solidified -->
    <div class="pubqueue-header">
        <h1>Accounting Office</h1>
    </div>

    <div class="publicmain">
        <div class="officewindows-container">
            <div class="officewindows-col">
                <div class="officewindows">
                    <div class="window-up">
                        <div class="window">
                            <h2>Window 1</h2>
                            <p>Now serving</p>
                            </div>
                        <div class="now-serving">
                            <p>NUL106</p>
                        </div>
                    </div>
                    <div class="window-down">
                        <div class="window">
                            <h2>Window 1</h2>
                            <p>Closed</p>
                        </div>
                    </div>
                </div>
                <div class="officewindows">
                    <div class="window-up">
                        <div class="window">
                            <h2>Window 2</h2>
                            <p class="service-up">Now serving</p>
                            </div>
                        <div class="now-serving">
                            <p>NUL108</p>
                        </div>
                    </div>
                    <div class="window-down">
                        <div class="window">
                            <h2>Window 2</h2>
                            <p>Closed</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="officewindows-col">
                <div class="officewindows">
                    <div class="window-up">
                        <div class="window">
                            <h2>Window 3</h2>
                            <p>Now serving</p>
                            </div>
                        <div class="now-serving">
                            <p>NUL109</p>
                        </div>
                    </div>
                    <div class="window-down">
                        <div class="window">
                            <h2>Window 3</h2>
                            <p>Closed</p>
                        </div>
                    </div>
                </div>
                <div class="officewindows">
                    <div class="window-up">
                        <div class="window">
                            <h2>Window 4</h2>
                            <p class="service-up">Now serving</p>
                            </div>
                        <div class="now-serving">
                            <p>NUL111</p>
                        </div>
                    </div>
                    <div class="window-down">
                        <div class="window">
                            <h2>Window 4</h2>
                            <p>Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="queue-div"></div>
        
        <div class="currentqueue">
            <div class="queue-container">
                <div class="queue-container-left q-cont">
                    <!-- first 5 in line here -->
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>1</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL112</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>2</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL113</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>3</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL114</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>4</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL115</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>5</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL116</p>
                        </div>
                    </div>
                </div>

                <div class="queue-container-right q-cont">
                    <!-- 6th to 10th in queue -->
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>6</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL119</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>7</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL113</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>8</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL114</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>9</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL115</p>
                        </div>
                    </div>
                    <div class="queue-element">
                        <div class="q-num">
                            <h3>10</h3>
                        </div>
                        <div class="q-code">
                            <p>NUL116</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
