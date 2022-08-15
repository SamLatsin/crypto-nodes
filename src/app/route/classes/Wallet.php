<?
namespace App;

class Wallet{
   public $app;
   public $model;

  function __construct($app,$model=null) {
      $this->app = $app;
      $this->model = 'MyApp\Models\Wallets';
  }

  function getWallets() {
    $phql = "SELECT * FROM ".$this->model." ORDER BY id DESC";
    // return $this->app->modelsManager;
    return $this->app->modelsManager->executeQuery($phql)->toArray();
  }

  function getWalletById($id){
    $phql = "SELECT * FROM ".$this->model." WHERE id=:id:";
    return $this->app->modelsManager->executeQuery($phql,['id'=>$id])->toArray();
  }

  function getWalletsByTicker($ticker) {
    $phql = "SELECT * FROM ".$this->model." WHERE ticker=:ticker:";
    return $this->app->modelsManager->executeQuery($phql,['ticker'=>$ticker])->toArray();
  }

  function getWalletByTickerAndName($ticker, $name) {
    $phql = "SELECT * FROM ".$this->model." WHERE ticker=:ticker: AND name=:name:";
    return $this->app->modelsManager->executeQuery($phql,['ticker'=>$ticker, 'name'=>$name])->toArray();
  }

  function getFileImportedWalletsByTicker($ticker) {
    $phql = "SELECT * FROM ".$this->model." WHERE ticker=:ticker: AND name like \"frw%\"";
    return $this->app->modelsManager->executeQuery($phql,['ticker'=>$ticker])->toArray();
  }

  function getWalletByTickerAndKey($ticker, $key) {
    $phql = "SELECT * FROM ".$this->model." WHERE ticker=:ticker: AND privateKey=:key:";
    return $this->app->modelsManager->executeQuery($phql,['ticker'=>$ticker, 'key'=>$key])->toArray();
  }

  function getWalletsByName($ticker) {
    $phql = "SELECT * FROM ".$this->model." WHERE name=:name:";
    return $this->app->modelsManager->executeQuery($phql,['name'=>$name])->toArray();
  }

  function deleteWallet($id){
    $phql  = "DELETE FROM ".$this->model." WHERE id = :id:";
    $res = $this->app->modelsManager->executeQuery($phql,['id'=>$id]);
    return $res;
  }

  function deleteWalletByName($name){
    $phql  = "DELETE FROM ".$this->model." WHERE name = :name:";
    $res = $this->app->modelsManager->executeQuery($phql,['name'=>$name]);
    return $res;
  }

  function insertWallet($fields){
    $phql = 'INSERT INTO '.$this->model;
    foreach ($fields as $key => $field) {
      $keys[] = $key;
      $values[] = ':'.$key.':';
    }
    $keyRes = implode(',',$keys);
    $valRes =  implode(',',$values);
    $phql = $phql.' ('.$keyRes.') VALUES ('.$valRes.')';
    $res = $this->app->modelsManager->executeQuery($phql,$fields);
    return  $res->getModel()->id;
  }

  function updateWallet($fields,$id=0,$upd=false){
    $phql = 'UPDATE '.$this->model.' SET ';
    foreach ($fields as $key => $field) {
      if($upd!=$key){
        $values[] = $key.'=:'.$key.':';
      }
    }
    $valRes =  implode(', ',$values);
    if(!$upd){
      $phql = $phql.$valRes.' WHERE id='.$id;
    }else{
      $phql = $phql.$valRes.' WHERE '.$upd.'=:'.$upd.':';
    }
    $res = $this->app->modelsManager->executeQuery($phql,$fields);
    return $res;
  }
}