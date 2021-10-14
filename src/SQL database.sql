use Tanpa_Nama;

create table Admins (
  UserID INT,
  Username VARCHAR (255),
  Passwords VARCHAR(100)
);

create table Reports (
    Submission_Date DATE,
    Submission_Time TIME,
    Report_Title varchar (100),
    ReportID int,
    Report_Description varchar (255), 
    
)