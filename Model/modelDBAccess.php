<?php
class modelDBAccess{
    private ?PDO $PDOInstance = null;
    private static ?modelDBAccess $instance = null;
    private function __construct()
    {
        try {
            $this->PDOInstance=new PDO('mysql:host=localhost;dbname=rea705','root','root');

        } catch (Exception $e){
            $_SESSION['Page'] = 'Error';
            $_SESSION['Error'] = 'Connexion Error';
        }
    }
    public static function getInstance(): ?modelDBAccess
    {
        if(is_null(self::$instance)){
            self::$instance = new modelDBAccess();
        }
        return self::$instance;
    }
    public function getReports(): bool|array
    {
        $sql = "select * from `rea705`.Reports";
        return self::sendSql($sql);
    }
    public function sendSql($sql): bool|array
    {
        try {
            $query = $this->PDOInstance->prepare($sql);
            $query->execute();
            $tab = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e){
            $tab = false;
        }
        return $tab;
    }
}