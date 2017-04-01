<?php

namespace App\Helpers;

class Esports
{
    public static function createSmiteSession($signature) {
        $s = "/";
        $utc_timestamp = gmdate('YmdHis');
        $session_url = config('esports.SMITE.SMITE_API').config('esports.SMITE.SMITE_SESSION').config('esports.SMITE.JSON_FORMAT').config('esports.SMITE.SMITE_DEV_ID').$s.$signature.$s.$utc_timestamp;

        $ch = curl_init($session_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $session = curl_exec($ch);
        curl_close($ch);
        $session = json_decode($session);

        return $session->session_id;
    }

    public static function createSmiteSignature($endpoint) {
        $utc_timestamp = gmdate('YmdHis');
        $md5_string = config('esports.SMITE.SMITE_DEV_ID').$endpoint.config('esports.SMITE.SMITE_AUTH_KEY').$utc_timestamp;
        $signature = md5($md5_string);
        return $signature;
    }

    public static function getSmitePlayer($signature, $player, $session_id) {
        $utc_timestamp = gmdate('YmdHis');
        $s = "/";
        $session_url = config('esports.SMITE.SMITE_API').config('esports.SMITE.SMITE_PLAYER').config('esports.SMITE.JSON_FORMAT').config('esports.SMITE.SMITE_DEV_ID').$s.$signature.$s.$session_id.$s.$utc_timestamp.$s.$player;
        $ch = curl_init($session_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $player_object = curl_exec($ch);
        curl_close($ch);
        $player_object = json_decode($player_object);

        return $player_object;
    }

    public static function getMatchHistory($signature, $player, $session_id)
    {
        $utc_timestamp = gmdate('YmdHis');
        $s = "/";
        $session_url = config('esports.SMITE.SMITE_API').config('esports.SMITE.SMITE_MATCHHISTORY').config('esports.SMITE.JSON_FORMAT').config('esports.SMITE.SMITE_DEV_ID').$s.$signature.$s.$session_id.$s.$utc_timestamp.$s.$player;

        $ch = curl_init($session_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $match_history = curl_exec($ch);
        curl_close($ch);
        $match_history = json_decode($match_history);

        return $match_history;
    }

    public static function getTopMatches($signature, $session_id)
    {
        $utc_timestamp = gmdate('YmdHis');
        $s = "/";
        $session_url = config('esports.SMITE.SMITE_API').config('esports.SMITE.SMITE_GETTOPMATCHES').config('esports.SMITE.JSON_FORMAT').config('esports.SMITE.SMITE_DEV_ID').$s.$signature.$s.$session_id.$s.$utc_timestamp;
        var_dump($session_url);
        $ch = curl_init($session_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $top_matches = curl_exec($ch);
        curl_close($ch);
        $top_matches = json_decode($top_matches);

        return $top_matches;
    }
}
