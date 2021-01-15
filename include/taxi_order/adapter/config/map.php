<?php


return [
    "enableMap" => false,
    "zoom" => 10,
    "mapAIconSrc" => '/include/taxi_order/adapter/images/i/a_icon.png',
    "mapAIconSize" => "[24, 27]",
    "mapAIconAnchor" => "[12, 27]", // point of the icon which will correspond to marker's location"
    "mapBIconSrc" => "/include/taxi_order/adapter/images/i/b_icon.png",
    "mapBIconSize" => "[24, 27]",
    "mapBIconAnchor" => "[12, 27]",
    "mapLineStyle" => [
        [
            "color" => "black",
            "opacity" => 0.15,
            "weight" => 9,
        ],
        [
            "color" => "white",
            "opacity" => 0.8,
            "weight" => 6,
        ],
        [
            "color" => "#4e4e4e",
            "opacity" => 1,
            "weight" => 2,
        ],
    ],
    "mapMissingLineStyle" => [
        [
            "color" => "black",
            "opacity" => 0.15,
            "weight" => 7,
        ],
        [
            "color" => "white",
            "opacity" => 0.6,
            "weight" => 4,
        ],
        [
            "color" => "gray",
            "opacity" => 0.8,
            "weight" => 2,
            "dashArray" => "7,12"
        ],
    ],
    "mapCarsUpdateTimeout" => 20000,
    "mapRedCarIconSrc" => "/include/taxi_order/adapter/images/i/red_car.png",
    "mapRedCarIconSize" => "[24, 27]",
    "mapRedCarIconAnchor" => "[12, 27]",
    "mapRedCarPopupAnchor" => "[0, -27]", // point from which the popup should open relative to the iconAnchor"
    "mapGreenCarIconSrc" => "/include/taxi_order/adapter/images/i/green_car.png",
    "mapGreenCarIconSize" => "[24, 27]",
    "mapGreenCarIconAnchor" => "[12, 27]",
    "mapGreenCarPopupAnchor" => "[0, -27]",
];