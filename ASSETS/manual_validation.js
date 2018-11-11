function validate_email(str){
    if(str.split(' ').length>1)return false;
    console.log("No Spaces");
    s1=str.split('@');
    if((s1.length-1)>1)return false;
    console.log("One @");
    if(s1[0]=="") return false;
    console.log("email not blank");
    s2=s1[1].split('.');
    if(s2.length!=2)return false
    if(s2[0]=="")return false;
    if(s2[1]=="")return false;
    return true;
}