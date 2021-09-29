import java.util.*;
import java.io.File;
import java.text.*;

public class report {
    private String title;
    private String description;
    private Date eventDate;
    private Date submittedDate;
    private ArrayList <File> filesList;


    public report(String title, String description, Date submittedDate, Date eventDate, ArrayList filesList){
        super();
        setTitle(title);
        setDescription(description);
        setFilesList(filesList);
        setEventDate();
        eventDate = new Date();
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title){
        this.title = title;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description){
        this.description = description;
    }

    public Date getEventDate() {
        return eventDate;
    }

    public void setEventDate() {
        this.eventDate = new Date();
    }

 /*   public void validateEventDate(Date eventDate) {


      }
*/

    public void setFilesList(ArrayList<File> filesList) {
        this.filesList = filesList;
    }

    public ArrayList<File> getFilesList() {
        return filesList;
    }
}

