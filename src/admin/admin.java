package admin;

public class admin {
    private String password;

    public admin(String password){
        super();
        setPassword(password);

    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }
}
