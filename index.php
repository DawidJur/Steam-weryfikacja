<meta charset="utf-8">
<title>Steam Games checking ID</title>
<form action="" method="get">
	<p>Nick, URL after /id/ <input type="text" name="id"> <input type="submit"></p>
	<?php
class TaskVerification {
	public $GameSteamID = "690610";
	
	private function SteamFinderURL(){
		$this->steamFinderURL = "https://steamid.io/lookup/";
		$this->id = $_GET['id'];
		return ($this->steamFinderURL.$this->id = $_GET['id']);
	}
	
	public function getPlayerID64() {
		$this->SteamFinderURL = $this->SteamFinderURL();
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_URL,$this->SteamFinderURL);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,1);
		$this->SteamFinderHTML = curl_exec($this->curl);
		curl_close($this->curl);
		$this->tab1 = explode("steamID64</dt>", $this->SteamFinderHTML);
		$this->tab2 = explode("data-clipboard-text=\"", $this->tab1[1]);
		$this->tab3 = explode("\" src=\"", $this->tab2[1]);
		$this->GetPlayerID64 = $this->tab3[0];
		return $this->GetPlayerID64;
	}
	
 private function getTimeJSON() {
		$this->SteamAPIUrl = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=BB63045D0A5A74428B323EC16FB987EF&steamid=".$this->getPlayerID64()."&format=json";
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_URL,$this->SteamAPIUrl);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,1);
		$this->APIJson = curl_exec($this->curl);
		curl_close($this->curl);
		$this->json = json_decode($this->APIJson);
		return $this->json;
	}
	
	public function getPlayTime() {
		$this->gameArrays = $this->getTimeJSON()->response->games;
		if(!empty($this->gameArrays)){
			foreach ($this->gameArrays as $table) {
				if($table->appid == $this->GameSteamID){
					return $table->playtime_forever;
				}
			}
		}	
	}
	
	private function getAchievementsJSON() {
		$this->SteamAPIUrl = "http://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v0001/?appid=690610&key=BB63045D0A5A74428B323EC16FB987EF&steamid=".$this->getPlayerID64();
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_URL,$this->SteamAPIUrl);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,1);
		$this->APIJson = curl_exec($this->curl);
		curl_close($this->curl);
		$this->json = json_decode($this->APIJson);
		return $this->json; 
	}
	
	public function getAchievements($AchievementName) {
		$this->AchievementsArray = $this->getAchievementsJSON()->playerstats->achievements;
		if(!empty($this->AchievementsArray)){
			foreach ($this->AchievementsArray as $table) {
				if($table->apiname == $AchievementName){
					return $table->achieved;
				}
			}
		}
	}
	
	public function getFeedback() {
		$this->playTime = $this->getPlayTime();
		$this->Achiev1 = $this->getAchievements("JudgeBronze");
		$this->Achiev2 = $this->getAchievements("AssassinBronze");
		
		return ("User ID64: ".$this->getPlayerID64()."<br /> 
		Playtime is: ".$this->playTime." minutes <br />
		Achievement JudgeBronze = ".$this->Achiev1."<br />
		Achievement AssassinBronze = ".$this->Achiev2);
	}
}

$v = new TaskVerification;
echo($v->getFeedback());
	
?>
