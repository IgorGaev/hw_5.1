<?php
require 'vendor/autoload.php';

$api = new \Yandex\Geo\Api();

// искать по адресу
$adress = filter_input(INPUT_POST, 'adress');
$lat = filter_input(INPUT_GET, 'lat');
$long = filter_input(INPUT_GET, 'long');


$api->setQuery($adress);

// Настройка фильтров
$api
        ->setLimit(50) // кол-во результатов
        ->setLang(\Yandex\Geo\Api::LANG_RU) // локаль ответа
        ->load();

$response = $api->getResponse();
$response->getFoundCount(); // кол-во найденных адресов
// Список найденных точек
$collection = $response->getList();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="style.css" rel="stylesheet">
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript">
        </script>
        <?php if (isset($lat) && isset($long)) { ?>
            <script type="text/javascript">
                ymaps.ready(init);
                var myMap,
                        myPlacemark;

                function init() {
                    myMap = new ymaps.Map("map", {
                        center: [<?= $lat ?>, <?= $long ?>],
                        zoom: 7
                    });

                    myPlacemark = new ymaps.Placemark([<?= $lat ?>, <?= $long ?>], {
                        hintContent: 'Москва!',
                        balloonContent: 'Столица России'
                    });

                    myMap.geoObjects.add(myPlacemark);
                }
            </script><?php } ?>
        <title>Найти координаты</title>
    </head>
    <body>
        <form method="POST">
            <input type="text" name="adress" value="" />
            <input type="submit" value="Найти" />
        </form>
        <?php if (isset($adress)) { ?>
            <table >
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Адрес</th>
                        <th>Широта</th>
                        <th>Долгота</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($collection as $num => $item){
                        $adrs = $item->getAddress();
                        $lat = $item->getLatitude();
                        $long = $item->getLongitude();
                        ?>
                        <tr>
                            <td><?= ++$num ?></td>
                            <td><a href='index.php?lat=<?= $lat ?>&long=<?= $long ?>'><?= $adrs ?></a></td>
                            <td><?= $lat ?></td>
                            <td><?= $long ?></td>
                        </tr>
                    <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <div id="map" style="width: 600px; height: 400px"></div>

    </body>
</html>
