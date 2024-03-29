cat > $0.cmd << fin
{
    if (\$1==par) {
       print \$2
    }
}
fin
DB_HOST=`cat .env | awk -v par=DB_HOST_CH -F = -f $0.cmd`
DB_DATABASE=`cat .env | awk -v par=DB_DATABASE_CH -F = -f $0.cmd`
DB_USERNAME=`cat .env | awk -v par=DB_USERNAME_CH -F = -f $0.cmd`
DB_PASSWORD=`cat .env | awk -v par=DB_PASSWORD_CH -F = -f $0.cmd`
echo $DB_HOST
echo $DB_DATABASE
export PGPASSWORD=$DB_PASSWORD
##DB_DATABASE=template1
##DB_USERNAME=postgres
##B_HOST=localhost
cat > $0.sql << fin
CREATE OR REPLACE FUNCTION DateDiff (units VARCHAR(30), start_t timestamp, end_t timestamp)
     RETURNS numeric AS \$\$
   DECLARE
     diff time = null;
     total numeric(8)=0; 
   BEGIN
     diff = end_t - start_t;
     total = (date_part('hour',diff)*60*60)+(date_part('minute',diff)*60)+date_part('second',diff);
     RETURN total;
   END;
   \$\$ LANGUAGE plpgsql;
select "ENTRADA",current_timestamp, datediff('SS',"ENTRADA",CURRENT_TIMESTAMP::timestamp(0)),current_timestamp-"ENTRADA" from "CHAT_ESPERA";
fin
psql -h $DB_HOST -d $DB_DATABASE -U $DB_USERNAME  < $0.sql
rm $0.cmd
rm $0.sql

