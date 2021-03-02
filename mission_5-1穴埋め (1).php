<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>
    <h1>簡易掲示板</h1>
    
    <?php
    // DB接続設定
    $dsn='mysql:dbname="データベース名";host=localhost';
    $user='ユーザー名';
    $password='パスワード';
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    
    $sql="CREATE TABLE IF NOT EXISTS m51"//もしまだこのテーブルが存在しないなら、テーブルを作成
    . "("//以下テーブル(tbtest)に登録する項目
    . "id INT AUTO_INCREMENT PRIMARY KEY,"//自動で登録されていうナンバリング
    . "name char(32) NOT NULL,"//名前を入れる。文字列、半角英数で32文字
    . "comment TEXT NOT NULL,"//コメントを入れる。文字列、長めの文章も入る
    . "password TEXT NOT NULL,"
    . "DATETIME DATETIME"
    . ");";
    $stmt=$pdo->query($sql); //query関数はデータベースに$sqlを届ける役割
    
    
    
    
    // 新規投稿
     if(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["pass"])&&empty($_POST["edit_num"])){  //名前とコメントが空じゃなくて編集番号がからだったら
    $name=$_POST["name"]; //名前フォームに書き込まれた内容を変数に代入
    $comment=$_POST["comment"]; //コメントフォームに書き込まれた内容を変数に代入
    $password=$_POST["pass"];
    
    $sql=$pdo->prepare("INSERT INTO m51 (name,comment,password,DATETIME) VALUES(:name,:comment,:password,cast(now() as datetime))");//cast(now()as datetie)←動かなかったらこれで書く
    
                                                                                                                                                        
    $sql->bindParam(':name',$name,PDO::PARAM_STR);
    $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
    $sql->bindParam(':password',$password,PDO::PARAM_STR);
    
    
    $sql->execute();
     }
    
    
    //削除処理
    if(!empty($_POST["delete"])&&!empty($_POST["delete_pass"])){
        $delete=$_POST["delete"];
        $deletepass=$_POST["delete_pass"];
    $sql=$pdo->prepare("delete from m51 where id=:id and password=:password");//delete文で登録したデータレコードの内容を削除
    $sql->bindParam(':id',$delete,PDO::PARAM_INT);//idは$deleteから情報もらってるよ
    $sql->bindParam(':password',$deletepass,PDO::PARAM_STR);//passwordは$deletepassから情報もらってるよ
    $sql->execute();
    }
    
    //編集処理 サーバーに保存したデータを呼び出すにはSERECT文が必要
    if(!empty($_POST["edit"])&&!empty($_POST["edit_pass"])){ //編集番号と編集パスワードが空じゃないとき
    $edit=$_POST["edit"];
    $editpass=$_POST["edit_pass"];
    
    $sql=$pdo->prepare("SELECT * FROM m51 where id=:id and password=:password");

    $sql->bindParam(':id',$edit,PDO::PARAM_INT);//idは$deleteから情報もらってるよ

    $sql->bindParam(':password',$editpass,PDO::PARAM_STR);//passwordは$deletepassから情報もらってるよ
    $sql->execute();
    
    $editresults=$sql->fetchAll();
    
    foreach($editresults as $editresult){//ループ処理
    $edit_num=$editresult[0];
    $edit_name=$editresult[1];
    $edit_comment=$editresult[2];
    $edit_password=$editresult[3];
    }
    }

    //編集投稿機能
    if(!empty($_POST["edit_num"])&&!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["pass"])){
    $id=$_POST["edit_num"];
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $pass=$_POST["pass"];
    
    $sql='UPDATE m51 SET name=:name,comment=:comment,DATETIME=cast(now() as datetime) WHERE id=:id and password=:password' ;
    $sql = $pdo->prepare($sql);
    $sql->bindParam(':name',$name,PDO::PARAM_STR);//idは$deleteから情報もらってるよ
    $sql->bindParam(':comment',$comment,PDO::PARAM_STR);//passwordは$deletepassから情報もらってるよ
    $sql->bindParam(':id',$id,PDO::PARAM_INT);//idは$deleteから情報もらってるよ
    $sql->bindParam(':password',$pass,PDO::PARAM_STR);//passwordは$deletepassから情報もらってるよ
    $sql->execute();
    
    }
    
    
    
?>
    
    <!--入力フォーム-->
    <form action="" method="post" name="write">
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($edit_name)){echo $edit_name;} ?>" required><br>
        <input type="text" name="comment" placeholder="コメント"  size="50" value="<?php if(isset($edit_comment)){echo $edit_comment;} ?>" required>
        <input type="hidden" name="edit_num" value="<?php if(isset($edit_num)){echo $edit_num;} ?>">
        <br>
        <input type ="password" name ="pass" placeholder ="パスワード"  value="<?php if(isset($edit_password)){echo $edit_password;} ?>" required>
        <input type="submit" name="submit">    
    </form>
    <!--削除フォーム-->
    <form action="" method="post">
        <input type="number" name="delete" placeholder="削除対象番号"><br>
        <input type ="password" name ="delete_pass" placeholder ="パスワード">
        <input type="submit" name="submit2" value="削除">
    </form>
    <!--編集フォーム-->
    <form action="" method="post">
        <input type="number" name="edit" placeholder="編集対象番号"><br>
        <input type ="password" name ="edit_pass" placeholder ="パスワード">
        <input type="submit" name="submit3" value="編集">
    </form>
    <!--DBのテーブルの中身をid毎に表示-->
<?php

$sql='SELECT * FROM m51';
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();// fetchAll:結果セットに残っている全ての行を含む配列を返す
    foreach($results as $row){
        echo  "投稿番号:".$row['id'].'<br>';
		echo  "名前:".$row['name'].'<br>';
		echo  "コメント:".$row['comment'].'<br>';
		echo  "日時:".$row['DATETIME'].'<br>';
        echo "<hr>";
    }
?>
 </body>
 </html>