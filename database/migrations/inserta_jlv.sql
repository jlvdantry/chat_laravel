select max(id_operador) from CHAT_LOCATEL.CHAT_OPERADORES ;
Insert into CHAT_LOCATEL.CHAT_OPERADORES (ID_OPERADOR,NOMBRE,PSEUDONIMO,LOGIN,PASS,INICIO,SALIDA,IP,PERFIL,STATUS,ID_INSTITUCION,HOST,NAVEGADOR) values (1056,'Jose Luis vasquez','D_jlv','jlvdantry','6443e3eabf4618647f62b16bb9c33cdd',to_timestamp('05/11/21 09:11:14.431000000','DD/MM/RR HH24:MI:SS.FF'),to_timestamp('05/11/21 10:28:46.901000000','DD/MM/RR HH24:MI:SS.FF'),'187.189.198.3',1,0,1,null,null);
update CHAT_LOCATEL.CHAT_OPERADORES  set status=0 where id_operador=1056;
