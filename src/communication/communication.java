package communication;

import javax.net.ssl.SSLSocket;
import javax.net.ssl.SSLSocketFactory;
import java.io.*;

import communication.socket;

public class communication {
    private final socket clientSocket = new socket();
    /*
        private static final String[] protocols = new String[] {"TLSv1.3"};
        private static final String[] cipher_suites = new String[] {"TLS_AES_128_GCM_SHA256"};
        */
 public SSLSocket makeSocket() throws IOException {
     // socket.setEnabledProtocols(protocols);
     // socket.setEnabledCipherSuites(cipher_suites);
     return (SSLSocket) SSLSocketFactory.getDefault().createSocket(clientSocket.getServer(), clientSocket.getPort());

 }
 public void test() throws Exception{
     try (SSLSocket socket = makeSocket()) {
         InputStream is = new BufferedInputStream(socket.getInputStream());
         OutputStream os = new BufferedOutputStream(socket.getOutputStream());
         String message = "hi";
         os.write(message.getBytes());
         os.flush();
         byte[] data = new byte[2048];
         int len = is.read(data);
         if (len <= 0) {
             throw new IOException("no data received");
         }
         System.out.printf("client received %d bytes: %s%n",
                 len, new String(data, 0, len));
     }
 }

}
