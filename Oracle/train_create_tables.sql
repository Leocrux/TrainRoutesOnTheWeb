drop table users cascade constraints;
drop table trains cascade constraints;
drop table mainroute cascade constraints;
drop table internalroute cascade constraints;
drop table statii cascade constraints;
drop table statie_tren cascade constraints;
drop table trasee cascade constraints;
drop table user_saved cascade constraints;
drop type prevList;


--CREATE TYPE ARRTYPE IS TABLE OF VARCHAR(20) INDEX BY BINARY_INTEGER;
CREATE TYPE prevList AS VARRAY(1000) OF integer;

CREATE TABLE user_saved (  -- create database table
   u_id integer NOT NULL,
   numberof integer,
   prev prevList,
   train prevList
);
   



create table users(
	u_id integer NOT NULL, --pk
	nume varchar2(10) NOT NULL,
	prenume varchar2(10) NOT NULL,
	email varchar2(30) NOT NULL,
	username varchar2(10) NOT NULL,
	password varchar2(20) NOT NULL
)
/
create table trains(
	tr_id integer not null, --pk
	category integer
)
/
create table mainroute(
	mr_id integer not null, --pk
	tr_id integer not null, --fk trains
	source_id integer not null, 
	dest_id integer not null,
	lungime integer,
	dep_hour integer,
	arr_hour integer,
	internals integer
)
/
create table internalroute(
	ir_id integer not null,--pk
	mr_id integer not null,--fk mainroute
	secv_nr integer, 
	source_id integer,
	dest_id integer,
	source_name varchar2(50),
	dest_name varchar2(50),
	dep_hour integer,
	arr_hour integer,
	delay_sec integer,
  time_to integer,
  leni integer
)
/

create table statii(
	s_id integer not null, --pk
	name varchar(50),
	trains integer
)
/
create table statie_tren(
	s_id integer not null, --fk statii
	tr_id integer not null --fk train
)
/
create table trasee(
	u_id integer not null, --fk users
	dest_id integer not null,
	source_id integer not null,
	dep_hour integer,
	arr_hour integer
)
/

--end create tables--

---- pk and fks------
ALTER TABLE users ADD PRIMARY KEY (u_id);
ALTER TABLE trains ADD PRIMARY KEY(tr_id);

ALTER TABLE mainroute ADD PRIMARY KEY(mr_id);

ALTER TABLE mainroute ADD FOREIGN KEY (tr_id) REFERENCES trains(tr_id);


ALTER TABLE internalroute ADD PRIMARY KEY(ir_id);
ALTER TABLE internalroute ADD FOREIGN KEY(mr_id) REFERENCES mainroute(mr_id);


ALTER TABLE statii ADD PRIMARY KEY(s_id);

ALTER TABLE statie_tren ADD FOREIGN KEY(s_id) REFERENCES statii(s_id);
ALTER TABLE statie_tren ADD FOREIGN KEY(tr_id) REFERENCES trains(tr_id);

ALTER TABLE trasee ADD FOREIGN KEY(u_id) REFERENCES users(u_id);

----end pk and fk-------------------------------------




---index for djksrta-----------
drop index src_id;

CREATE INDEX src_id ON internalroute(source_id);
----end index----------------


---trigger for auto increase pk in main route---
create or replace trigger pk_mr
before insert on mainroute
for each row

declare
  n_mr_id integer;
begin
  select max(mr_id) into n_mr_id from mainroute;
  IF n_mr_id IS NULL then n_mr_id:=0;
  end if;
  :NEW.mr_id := n_mr_id+1;
end;

-----------end trigger for pk autoincrease in mainroute-------------


-------------trigger for auto increment user_saved --------
create or replace trigger pk_us
before insert on user_saved
for each row

declare
  n_mr_id integer;
begin
  select max(numberof) into n_mr_id from user_saved where u_id= :NEW.u_id;
  
  IF n_mr_id IS NULL then n_mr_id:=0;
  end if;
  :NEW.numberof := n_mr_id+1;
end;
-----------end trigger user_saved--------



---trigger for auto adding internals to internal route-------
CREATE OR REPLACE TRIGGER add_internal
BEFORE INSERT ON internalroute
FOR EACH ROW
DECLARE
  n_mr_id integer;
  n_ir_id integer;
  v_internals mainroute.internals%TYPE;


  v_tr_id integer;
  v_s_id integer;
  v_count integer;
  aux integer;
BEGIN
--pk internal
  select max(ir_id) into n_ir_id from INTERNALROUTE;

  if n_ir_id IS NULL THEN 
    n_ir_id:=0;
  end if;

  :NEW.ir_id := n_ir_id+1;
  
--internals din mr ++
  n_mr_id := :NEW.mr_id;
  select internals into v_internals from mainroute where mr_id=n_mr_id;
  v_internals := v_internals+1;
  update mainroute set internals = v_internals where mr_id=n_mr_id;--where blabla




  --adaug in statii : dest/src
  select mr_id, tr_id into aux, v_tr_id from MAINROUTE where :NEW.mr_id = mr_id;

  select count(*) into v_s_id from statii where s_id = :NEW.source_id;


  IF v_s_id = 0 THEN 
    insert into statii(s_id,name) values (:NEW.source_id,:NEW.source_name);
  END IF;

  select count(*) into v_s_id from statii where s_id = :NEW.dest_id;


  IF v_s_id = 0 THEN 
    insert into statii(s_id,name) values (:NEW.dest_id,:NEW.dest_name);
  END IF;


--mapez statie tren
  select count(*) into v_count from statie_tren where s_id=:NEW.source_id and tr_id=v_tr_id;
  IF v_count = 0 THEN 
    insert into statie_tren(tr_id,s_id) values(v_tr_id, :NEW.source_id);
  END IF;

  select count(*) into v_count from statie_tren where s_id=:NEW.dest_id and tr_id=v_tr_id;
  IF v_count = 0 THEN 
    insert into statie_tren(tr_id,s_id) values( v_tr_id, :NEW.dest_id);
  END IF;

END;


--end trigger for auto adding internal route----