
-- поиск изменения подстатуса
select *
from tbl_order_act_log
where values::hstore ? 'substatus'
and values::hstore->'substatus' = '85'
and ins_date between '29.10.2016' and now()

-- увеличение на кол-во дней
WHERE (tbl.delivery_date - INTERVAL '1 days' * (tbl.days_after + tbl.storage_life)) <= NOW()

-- привилегии
GRANT ALL PRIVILEGES ON TABLE caru.bnd_prod_instruction TO postgres;
GRANT ALL PRIVILEGES ON TABLE caru.bnd_prod_instruction TO erp;
GRANT ALL PRIVILEGES ON caru.bnd_prod_instruction_id_seq TO postgres;
GRANT ALL PRIVILEGES ON caru.bnd_prod_instruction_id_seq TO erp;