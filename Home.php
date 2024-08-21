
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coinranking</title>
    <link rel="icon" type="image/png" sizes="16x16"
        href="https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Bitcoin.svg/2048px-Bitcoin.svg.png" />
</head>
<style>
    .card-body-1 {
        flex: 1 1 122px;
        padding: var(--bs-card-spacer-y) var(--bs-card-spacer-x);
        background-color: #f2f3f4;
    }

    .card-body-2 {
        flex: 1 1 160px;
        padding: var(--bs-card-spacer-y) var(--bs-card-spacer-x);
        background-color: #f2f3f4;
    }

    div.col-md-4 {
        font-size: 18px;
    }

    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;

        animation: spin 2s linear infinite;
    }


    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .image-container img {
        display: none;
    }

    #search-results {
        margin-top: 20px;
    }
</style>

<body>
    <?php


    include ('setup/set.php');

    ?>
    <div class="container">
        <header class="p-3 mb-3 border-bottom">
            
        </header>


        <?php
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "https://api.coinranking.com/v2/coins",

                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-access-token: your-api-key" //token
                ),
            )
        );


        $response = curl_exec($curl);


        curl_close($curl);
        $result = json_decode($response, true);

        $data = $result['data']['coins'];

        usort($data, function ($a, $b) {
            return $a['rank'] <=> $b['rank'];
        });

        $topCoins1 = array_slice($data, 0, 1);
        $topCoins2 = array_slice($data, 1, 1);
        $topCoins3 = array_slice($data, 2, 1);

        $topCoinsother = array_slice($data, 3);

        $columnsPerRow = 3;
        $rowCount = 0;
        $rows = array_chunk($topCoinsother, $columnsPerRow);

        
        ?>


        <div class="container">
            <form id="search-form" class="col-12" role="search" method="post"
                action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
                <input type="search" id="search-input" name="search"
                    class="form-control form-control-Light text-bg-Light" placeholder="Search..." aria-label="Search">
                <div id="search-results"></div>
            </form><br>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const searchInput = document.getElementById('search-input');
                    const searchResults = document.getElementById('search-results');
                    const content = document.getElementById('content'); // ใช้สำหรับซ่อนเนื้อหาต้นฉบับ
                    let timeout = null;

                    searchInput.addEventListener('input', function () {
                        clearTimeout(timeout);
                        const query = searchInput.value.trim();

                        if (query === '') {
                            // แสดงเนื้อหาต้นฉบับเมื่อไม่มีคำค้นหา
                            content.style.display = 'block';
                            searchResults.innerHTML = ''; // ล้างผลลัพธ์การค้นหา
                        } else {
                            // ซ่อนเนื้อหาต้นฉบับเมื่อมีคำค้นหา
                            content.style.display = 'none';

                            timeout = setTimeout(function () {
                                fetchResults(query);
                            }, 300); // ตั้งค่าดีเลย์เป็น 300 มิลลิวินาที
                        }
                    });

                    function fetchResults(query) {
                        searchResults.innerHTML = 'Searching...'; // แสดงสถานะการค้นหา

                        fetch('search.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                search: query
                            })
                        })
                            .then(response => response.text())
                            .then(data => {
                                searchResults.innerHTML = data; // แสดงผลลัพธ์
                            })
                            .catch(error => {
                                searchResults.innerHTML = 'เกิดข้อผิดพลาด';
                                console.error('Error:', error);
                            });
                    }
                });
            </script>





            <div id="content">
                <center>
                    <h5>Top <font style="color:#bf360c;">3</font> rank crypto</h5>
                </center><br>
                <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">

                    <?php foreach ($topCoins1 as $c1) {
                        $uuid = $c1['uuid'];
                        $price = $c1['price'];
                        $change = $c1['change']; ?>
                        <div class="col">
                            <div class="card">
                                <div class="card-body-2" class data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    data-id="<?php echo $uuid; ?>" Align=center><img src="<?php echo $c1['iconUrl'] ?>"
                                        width="50" high="50"><br><?php echo $c1['symbol']; ?><br><span style="color:#bdbdbd"
                                        ;><?php echo $c1['name']; ?></span><br><?php
                                           if ($change < 0) { ?>
                                        <span style="color:#bf360c;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                            </svg><?php echo abs($change) ?></span>
                                    <?php } ?>
                                    <?php if ($change > 0) { ?>
                                        <span style="color:#0a7e07;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                            </svg><?php echo $change ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php foreach ($topCoins2 as $c2) {
                        $uuid = $c2['uuid'];
                        $price = $c2['price'];
                        $change = $c2['change']; ?>
                        <div class="col">
                            <div class="card">
                                <div class="card-body-2" class data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    data-id="<?php echo $uuid; ?>" Align=center><img src="<?php echo $c2['iconUrl'] ?>"
                                        width="50" high="50"><br><?php echo $c2['symbol']; ?><br><span style="color:#bdbdbd"
                                        ;><?php echo $c2['name']; ?></span><br><?php
                                           if ($change < 0) { ?>
                                        <span style="color:#bf360c;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                            </svg><?php echo abs($change) ?></span>
                                    <?php } ?>
                                    <?php if ($change > 0) { ?>
                                        <span style="color:#0a7e07;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                            </svg><?php echo $change ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php foreach ($topCoins3 as $c3) {
                        $uuid = $c3['uuid'];
                        $price = $c3['price'];
                        $change = $c3['change']; ?>
                        <div class="col">
                            <div class="card">
                                <div class="card-body-2" class data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    data-id="<?php echo $uuid; ?>" Align=center><img src="<?php echo $c3['iconUrl']; ?>"
                                        width="50" high="50"><br><?php echo $c3['symbol']; ?><br><span style="color:#bdbdbd"
                                        ;><?php echo $c3['name']; ?></span><br><?php
                                           if ($change < 0) { ?>
                                        <span style="color:#bf360c;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                            </svg><?php echo abs($change) ?></span>
                                    <?php } elseif ($change > 0) { ?>
                                        <span style="color:#0a7e07;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                            </svg><?php echo $change ?></span>
                                    <?php } else { ?>
                                        <span style="color:#bdbdbd;"><?php echo $change ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div><br>





                <h5 Align=left>Top rank 4 to 50</h5>


                <?php foreach ($rows as $rowGroup) {


                    ?>
                    <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">

                        <?php foreach ($rowGroup as $row) {
                            $uuid = $row['uuid'];
                            $name = $row['name'];
                            $icon = $row['iconUrl'];
                            $price = (float) $row['price'];
                            $change = $row['change'];
                            ?>
                            <div class="col">
                                <div class="card">
                                    <div class="card-body-1" class data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        data-id="<?php echo $row['uuid']; ?>">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <img src="<?php echo $icon ?>" width="50">
                                            </div>
                                            <div class="col-md-6">
                                                <?php echo $name; ?><br>
                                                <p style="color:#bdbdbd" ;><?php echo $row['symbol']; ?></p>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                $<?php echo number_format($price, 2) ?>
                                                <br><?php
                                                if ($change < 0) { ?>
                                                    <span style="color:#bf360c;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-arrow-down"
                                                            viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd"
                                                                d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                                        </svg><?php echo abs($change) ?></span>
                                                <?php } elseif ($change > 0) { ?>
                                                    <span style="color:#0a7e07;"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-arrow-up"
                                                            viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd"
                                                                d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                                        </svg><?php echo $change ?></span>
                                                <?php } else { ?>
                                                    <span style="color:#bdbdbd;"><?php echo $change ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        <?php } ?>
                        
                    </div>
                    


                <?php } ?>
            </div>
            
        </div>
    </div>









    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <script>
                        let cachedCoins = {};

                        function fetchCoinData(id) {
                            const options = {
                                headers: {
                                    'x-access-token': 'your-api-key', //token
                                },
                            };

                            return fetch('https://api.coinranking.com/v2/coin/' + id, options)
                                .then((response) => response.json())
                                .then((result) => result.data.coin);
                        }

                        function loadAllCoinData() {

                            const ids = ['coin1', 'coin2', 'coin3'];
                            const promises = ids.map(id => fetchCoinData(id));

                            Promise.all(promises)
                                .then(coins => {
                                    coins.forEach(coin => {
                                        cachedCoins[coin.id] = coin;
                                    });
                                });
                        }

                        function formatPrice(amount) {
                            const priceFormatter = new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD',
                            });

                            if (amount >= 1_000_000_000_000) {
                                return `$${(amount / 1_000_000_000_000).toFixed(2)} Trillion`;
                            } else if (amount >= 1_000_000_000) {
                                return `$${(amount / 1_000_000_000).toFixed(2)} Billion`;
                            } else if (amount >= 1_000_000) {
                                return `$${(amount / 1_000_000).toFixed(2)} Million`;
                            } else {
                                return priceFormatter.format(amount);
                            }
                        }

                        function renderCoin(coin) {
                            const imgElement = document.querySelector('.coin__img');
                            const nameElement = document.querySelector('.coin__name');
                            const symbolElement = document.querySelector('.coin__symbol');
                            const priceElement = document.querySelector('.coin__price');
                            const marketCapElement = document.querySelector('.coin__marketCap');
                            const descriptionElement = document.querySelector('.coin__description');
                            const colorElement = document.querySelector('.coin__color');
                            const webElement = document.querySelector('.coin__web');


                            imgElement.src = coin.iconUrl;
                            nameElement.innerText = coin.name;
                            symbolElement.innerText = "(" + coin.symbol + ")";
                            priceElement.innerText = "PRICE " + formatPrice(coin.price);
                            marketCapElement.innerText = "Market Cap " + formatPrice(coin.marketCap);
                            descriptionElement.innerText = coin.description;
                            colorElement.style.color = coin.color;

                            document.getElementById('openWebsite').addEventListener('click', function () {
                                window.location.href = coin.websiteUrl;
                            });

                            imgElement.onload = function () {
                                imgElement.style.display = 'block';
                            }
                        }

                        $('#exampleModal').on('show.bs.modal', function (event) {
                            const button = $(event.relatedTarget); 
                            const id = button.data('id'); 
                            const modal = $(this);

                            if (cachedCoins[id]) {
                                renderCoin(cachedCoins[id]);
                            } else {
                                fetchCoinData(id)
                                    .then(coin => {
                                        cachedCoins[id] = coin;
                                        renderCoin(coin);
                                    });
                            }

                            modal.find('#modalId').text(id);
                        });

                        $(document).ready(function () {
                            loadAllCoinData(); // เรียกใช้ฟังก์ชันในการดึงข้อมูลทั้งหมดเมื่อหน้าโหลด
                        });
                    </script>
                    <div class="coin">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="image-container mt-2">
                                    <img class="coin__img" width="65">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <font class="coin__color"><span class="coin__name"></span></font>
                                <span class="coin__symbol"></span><br>
                                <span class="coin__price"></span><br>
                                <span class="coin__marketCap"></span></p>
                            </div>

                        </div>
                        <div class="row">
                            <span class="coin__description">
                                <center>
                                    <div class="loader"></div>
                                </center><br>
                                <h1></h1>
                            </span>
                        </div>





                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="openWebsite">GO TO WEBSITE</button>
                </div>
            </div>
        </div>
    </div>
    <br>


</body>

</html>