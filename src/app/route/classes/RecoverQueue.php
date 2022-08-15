<?
namespace App;

class RecoverQueue{
   public $app;
   public $model;

  function __construct($app,$model=null) {
      $this->app = $app;
      $this->model = 'MyApp\Models\RecoverQueue';
  }

  function getQueue() {
    $phql = "SELECT * FROM ".$this->model;
    // return $this->app->modelsManager;
    return $this->app->modelsManager->executeQuery($phql)->toArray();
  }

  function getItemById($id){
    $phql = "SELECT * FROM ".$this->model." WHERE id=:id:";
    return $this->app->modelsManager->executeQuery($phql,['id'=>$id])->toArray();
  }

  function getItemByTicker($ticker) {
    $phql = "SELECT * FROM ".$this->model." WHERE ticker=:ticker:";
    return $this->app->modelsManager->executeQuery($phql,['ticker'=>$ticker])->toArray();
  }

  function getItemByTickerAndName($ticker, $name) {
    $phql = "SELECT * FROM ".$this->model." WHERE ticker=:ticker: AND walletName=:name:";
    return $this->app->modelsManager->executeQuery($phql,['ticker'=>$ticker, 'name'=>$name])->toArray();
  }

  function getRecoverQueuesByName($ticker) {
    $phql = "SELECT * FROM ".$this->model." WHERE name=:name:";
    return $this->app->modelsManager->executeQuery($phql,['name'=>$name])->toArray();
  }

  function deleteItem($id){
    $phql  = "DELETE FROM ".$this->model." WHERE id = :id:";
    $res = $this->app->modelsManager->executeQuery($phql,['id'=>$id]);
    return $res;
  }

  function deleteItemByName($name){
    $phql  = "DELETE FROM ".$this->model." WHERE walletName = :name:";
    $res = $this->app->modelsManager->executeQuery($phql,['name'=>$name]);
    return $res;
  }

  function insertItem($fields){
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

  function updateItem($fields,$id=0,$upd=false){
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