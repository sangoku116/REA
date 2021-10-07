package communication;




public class socket {
    private final String server;
    private final int port;


    public socket(){
        super();
        server = "anynoumousServer";
        port = 1337;

    }

    public String getServer() {
        return server;
    }

    public int getPort() {
        return port;
    }

}
