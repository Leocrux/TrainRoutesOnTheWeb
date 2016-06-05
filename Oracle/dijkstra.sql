set serveroutput on;

CREATE OR REPLACE PACKAGE dijkstra AS
  TYPE ARRTYPE IS TABLE OF integer INDEX BY pls_INTEGER;
  PROCEDURE iobind_prev(c1 IN OUT ARRTYPE, pu_id in integer);
  procedure iobind_tren(c1 IN OUT ARRTYPE,pu_id IN integer);
  procedure drum(st IN integer, fin in integer, u_id in integer, opt in integer);
END dijkstra;
/
CREATE OR REPLACE PACKAGE BODY dijkstra AS

procedure drum(st IN integer, fin in integer, u_id in integer, opt in integer) is
  cursor vertices is
    SELECT s_id from statii;
  subtype vertex is vertices%rowtype;
  type vertex_set is table of vertex;
  Q vertex_set ;
  
--------
  type assocArray is table of pls_integer index by pls_integer;
  maxdist integer;
  imax integer;
  
  orase prevList;
  traseu prevList;
  
  curr_node integer;
  dist assocArray;
  prev assocArray;
  tren assocArray;
  alt integer;
  indx integer;
  maxi integer;
  fin_aux integer;
  cost_muchie integer;
  
begin
  maxdist := 2000000;
  alt:=2000000;

  open vertices;

  fetch vertices
    bulk collect into Q;
  close vertices;

if Q.Count > 0 then
  for i in Q.First .. Q.Last loop
    dist(Q(i).s_id) := 2000000;
    prev(Q(i).s_id) := -1;
    -- in_set(Q(i).s_id) := 1;
  end loop;
end if;

dist(st) := 0;


while Q.count>0 loop
  maxdist:=2000000;
  maxi:=0;
  indx:= Q.first;

    while indx is not null and indx!=0 loop
      if maxi=0 then maxi:=indx;
      else 
        if dist(Q(indx).s_id)<maxdist then
        maxdist:=dist(Q(indx).s_id);
        maxi:=indx;
        end if;
      end if;
      indx:=Q.next(indx);  
    end loop;

    curr_node := Q(maxi).s_id;
    Q.delete(maxi);

    for v in (SELECT dest_id ,leni, time_to, mr_id from internalroute where source_id = curr_node )
    LOOP
      if opt=1 then --cea mai scurta distanta
        cost_muchie := v.leni;
      else
        cost_muchie := v.time_to;
      end if;
      alt:= dist(curr_node) + cost_muchie;
      if alt < dist(v.dest_id) then
        dist(v.dest_id) := alt;
        prev(v.dest_id) := curr_node;
        Select tr_id into tren(v.dest_id) from mainroute where mr_id = v.mr_id; --ca sa ajung la ala(dest?!) iau trenul asta!
      end if;
    END LOOP;

end loop;

    fin_aux := fin;
    indx:=1;
    orase := prevList();
    traseu := prevList();
    
    while prev(fin_aux) != -1
    loop
      dbms_output.put_line(fin_aux||prev(fin_aux));
      dbms_output.put_line(fin_aux||tren(fin_aux));
      
      orase.extend(1);
      traseu.extend(1);
      orase(indx):=fin_aux;
      traseu(indx):=tren(fin_aux);
      fin_aux:=prev(fin_aux);
      indx:=indx+1;
    end loop;
      
      orase.extend(1);
      orase(indx):=fin_aux;
    
    
    insert into user_saved(u_id,prev,train) values (u_id,orase,traseu);
dbms_output.put_line('Distanta: ' || dist(fin));
END drum;


------------pentru scos array din user_saved---------
procedure iobind_prev(c1 IN OUT ARRTYPE,pu_id IN integer) is
    CURSOR CUR IS SELECT prev FROM user_saved where u_id = pu_id;
    ind integer;
    indx integer;
    P prevList;
begin
    ind:=1;
    indx:=1;
      P:=prevlist();
     
      open CUR; --haha

  
    loop
      FETCH CUR INTO P;
      EXIT WHEN CUR%NOTFOUND; 
        indx:= P.first;
        dbms_output.put_line('radu');
            while indx is not null and indx!=0 loop
            
              c1(ind) := P(indx);
              indx:=P.next(indx);  
              ind:=ind+1;
            end loop;
        c1(ind) := -1;
        ind:=ind+1;
     end loop;
     close cur;
DBMS_OUTPUT.PUT_LINE(ind);
end iobind_prev;



------------pentru scos array din user_saved---------
procedure iobind_tren(c1 IN OUT ARRTYPE,pu_id IN integer) is
    CURSOR CUR IS SELECT train FROM user_saved where u_id = pu_id;
    ind integer;
    indx integer;
    P prevList;
begin
    ind:=1;
    indx:=1;
      P:=prevlist();
     
      open CUR; --haha

  
    loop
      FETCH CUR INTO P;
      EXIT WHEN CUR%NOTFOUND; 
        indx:= P.first;
        dbms_output.put_line('radu');
            while indx is not null and indx!=0 loop
            
              c1(ind) := P(indx);
              indx:=P.next(indx);  
              ind:=ind+1;
            end loop;
        c1(ind) := -1;
        ind:=ind+1;
     end loop;
     close cur;
DBMS_OUTPUT.PUT_LINE(ind);
end iobind_tren;


END dijkstra;
  
----------end iobind-------------------
/
declare
c1 dijkstra.ARRTYPE;
begin  
  dijkstra.drum(21834,10938,1,1);
end;
