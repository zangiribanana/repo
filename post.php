<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
    <style>
        .bold{
            font-weight:bold;
        }
        
    </style>
</head>
<body>
    <div align="left" class="bold">この掲示板のテーマは好きな食べ物</div>
    <div align="left">パスワードはabcde</div>
    <div align="left">編集番号とパスワードを入力して編集ボタンを押すとコメント提出フォームに編集したい投稿の名前と投稿内容が移されます。編集後提出すると新規投稿ではなく、編集されます。</div>
    <br>
    <?php

    // DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    // 投稿用テーブルの作成
    // 自動裁判の投稿番号（id)、名前、コメント、投稿（編集）時間
    $sql = "CREATE TABLE IF NOT EXISTS posttable (id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "posttime TIMESTAMP"
    .");";
    // statementの略
    $stmt = $pdo->query($sql);

    
    
    ?>
    <?php 
        $post_time = date ("Y/m/d H:i:s");
        
        $editnumform="";
        $namestrform="";
        $commentstrform="";
        
        $password="abcde";
        
        $passwordform_c="";
        $passwordform_d="";
        $passwordform_e="";
        
        if(isset($_POST["delete"]) and !empty($_POST["delete_num"]) and $_POST["password_de"]==$password) {
            // 削除するとき
            
            // フォームから受け取った削除したい投稿番号が一致するレコードを削除する
            $passwordform_d=$_POST["password_de"];
            $id = $_POST["delete_num"];
            $sql = 'DELETE FROM posttable WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
             // 削除されることでidがずれるので更新する
            $sql = 'SET @n = 0';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            // 全てのデータのidカラムの値をnにする。nは1ずつ増やす。
            $sql = 'UPDATE posttable SET id=@n:=@n+1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $sql = 'ALTER TABLE posttable auto_increment = 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
        else if(isset($_POST["submit"]) and !empty( $_POST["name_str"]) and !empty($_POST["comment_str"]) and !empty($_POST["edit_num"]) and $_POST["password_com"]==$password){
            // コメント編集番号が入力されているとき、その番号の投稿を編集する
            $id = $_POST["edit_num"]; //変更する投稿番号
            $name =  $_POST["name_str"]; // 変更後の名前
            $comment = $_POST["comment_str"]; // 変更したいコメント
            $posttime = date ("Y/m/d H:i:s");
            $sql = 'UPDATE posttable SET name=:name,comment=:comment,posttime=:posttime WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':posttime', $posttime, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        else if(isset($_POST["submit"]) and !empty( $_POST["name_str"]) and !empty($_POST["comment_str"]) and $_POST["password_com"]==$password){
            // コメント追加するとき
            $name_str = $_POST["name_str"];
            $comment_str = $_POST["comment_str"];

            $sql = "INSERT INTO posttable (name, comment, posttime) VALUES (:name_str, :comment_str, :post_time)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name_str', $name_str);
            $stmt->bindParam(':comment_str', $comment_str);
            $stmt->bindParam(':post_time', $post_time);
            $stmt->execute();
            
        }
        else if(isset($_POST["edit"]) and !empty( $_POST["edit_num_p"]) and $_POST["password_ed"]==$password){
            // 編集ボタンを押した後、番号、名前、投稿内容を投稿フォームに写す

            $id = $_POST["edit_num_p"];

            $sql = 'SELECT * FROM posttable WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $editnumform = $id;
            $namestrform = $result['name'];
            $commentstrform = $result['comment'];
        }
        else{
            // デフォルトの入力内容
            $editnumform="";
            $namestrform="名無しさん";
            $commentstrform="ラーメン";
        }
        
    ?>
    
    <form action="" method="post">
        <input type="hidden" name="edit_num" value = "<?php echo $editnumform;?>" placeholder="編集する場合その投稿番号">
        <input type="text" name="name_str" value = "<?php echo $namestrform;?>" placeholder="名前">
        <input type="text" name="comment_str" value = "<?php echo $commentstrform;?>" placeholder="コメント">
        <input type="password" name="password_com" value = "<?php echo $passwordform_c;?>" placeholder="パスワード">
        <input type="submit" name="submit" value = "提出">
        <br>
        <!--削除番号指定フォーム-->
        削除番号
        <input type="number" name="delete_num">
        パスワード
        <input type="password" name="password_de" value = "<?php echo $passwordform_d;?>" placeholder="パスワード">
        <!--削除ボタン-->
        <input type="submit" name="delete" value = "削除">
        <br>
        <!--編集番号指定フォーム-->
        編集番号
        <input type="number" name="edit_num_p">
        パスワード
        <input type="password" name="password_ed" value = "<?php echo $passwordform_e;?>" placeholder="パスワード">
        <!--編集ボタン-->
        <input type="submit" name="edit" value = "編集">
        <br>
    </form>
    <?php
        echo "<hr>";
        $sql ='SELECT * FROM posttable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].", ";
            echo $row['name'].", ";
            echo $row['comment'].", ";
            echo $row['posttime'].'<br>';
            echo "<hr>";
        }
    ?>
</body>
</html>