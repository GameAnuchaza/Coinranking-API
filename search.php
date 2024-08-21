<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coinranking</title>
</head>


<body>
    <?php


    include('setup/set.php');
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', '/path/to/error.log');
    ?>
    <br>
    <div class="container">




        <?php
        if (isset($_POST['search']) && !empty($_POST['search'])) {
            $search = htmlspecialchars($_POST['search']);
            $MIN_SIMILARITY = 10; // กำหนดค่าความคล้ายคลึงขั้นต่ำที่ต้องการแสดง (เปอร์เซ็นต์)
        

            $curl = curl_init();


            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.coinranking.com/v2/coins", // แทนที่ด้วย URL ของ API จริง
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "x-access-token: your-api-key" // token
                ],
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                echo 'cURL Error: ' . curl_error($curl);
                curl_close($curl);
                exit;
            }


            curl_close($curl);


            $data = json_decode($response, true);

            if (isset($data['data']['coins'])) {
                $coins = $data['data']['coins'];

                // ชื่อของเหรียญที่คุณต้องการค้นหา
                $search_name = strtolower($search);
                // ฟังก์ชันในการค้นหาเหรียญตามชื่อ
                function findCoinsByName($coins, $name, $minSimilarity)
                {
                    $results = [];
                    foreach ($coins as $coin) {
                        $coinName = strtolower($coin['name']);
                        $searchName = strtolower($name);

                        // คำนวณความคล้ายคลึง
                        $similarityName = 0;
                        similar_text($coinName, $searchName, $similarityName);

                        // เพิ่มการกรองให้มีความคล้ายคลึงมากกว่า 80%
                        if ($similarityName >= $minSimilarity && stripos($coinName, $searchName) !== false) {
                            $coin['similarity'] = $similarityName;
                            $results[] = $coin;
                        }
                    }


                    usort($results, function ($a, $b) {
                        return $b['similarity'] <=> $a['similarity'];
                    });

                    return $results;
                }


                $results = findCoinsByName($coins, $search_name, $MIN_SIMILARITY);


                if (!empty($results)) {
                    $columnsPerRow = 3;
                    $rows = array_chunk($results, $columnsPerRow);
                    ?>
                    <h5 align="left">Buy, sell and hold crypto</h5>
                    <?php foreach ($rows as $rowGroup) { ?>
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
                                        <div class="card-body-1" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                            data-id="<?php echo $row['uuid']; ?>">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <img src="<?php echo $icon; ?>" width="50">
                                                </div>
                                                <div class="col-md-6">
                                                    <?php echo $name; ?><br>
                                                    <p style="color:#bdbdbd;"><?php echo $row['symbol']; ?></p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    $<?php echo number_format($price, 2); ?>
                                                    <br>
                                                    <?php if ($change < 0) { ?>
                                                        <span style="color:#bf360c;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                                class="bi bi-arrow-down" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd"
                                                                    d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                                            </svg>
                                                            <?php echo abs($change); ?>
                                                        </span>
                                                    <?php } elseif ($change > 0) { ?>
                                                        <span style="color:#0a7e07;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                                class="bi bi-arrow-up" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd"
                                                                    d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                                            </svg>
                                                            <?php echo $change; ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span style="color:#bdbdbd;"><?php echo $change; ?></span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php
                } else {
                    ?>
                    <br><br><br><br><br><br><br><br>
                    <center>
                        <h3>Sorry</h3><br>
                        <h5 style="color:#bdbdbd;">No results match this keyword</h5>
                    </center>
                    <?php
                }
            } else {
                ?>
                <br><br><br><br><br><br><br><br>
                <center>
                    <h3>Sorry</h3><br>
                    <h5 style="color:#bdbdbd;">No data available</h5>
                </center>
                <?php
            }
        } else {
            ?>
            <script>
                window.onload = function () {
                    window.location.replace("Home.php");
                };
            </script>
            <?php
        }
        ?>
    </div>

</body>

</html>