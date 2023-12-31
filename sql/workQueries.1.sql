begin;

-- изменение статуса
SELECT o.id
FROM tbl_order as o
JOIN tbl_order_delivery as od ON o.id = od.id
JOIN tbl_order_act_log as oal ON oal.order_id = o.id
WHERE o.status IN(13,14,15,16,17)
AND o.date_status BETWEEN '01.06.2015 00:00:00' AND '01.06.2015 23:59:59'
AND oal.method = 'setStatus'
AND (oal.values::hstore -> 'status_old')::integer = 11
AND args = 'a:1:{i:1;i:11;}'
AND od.type_id = 1

-- получить таблицу тарифов
SELECT dt.id, dt.delivery_type, dt.weight_from, dt.weight_to, dti.zona,dti.price, dt.tariff_type
FROM tbl_delivery_tariff as dt
LEFT JOIN tbl_delivery_tariff_item dti on dt.id = dti.delivery_tariff_id
WHERE dt.delivery_type = 4
AND dti.zona = 11
ORDER BY weight_from asc

-- получить аутсорсера
SELECT tbl.*
FROM (
    SELECT o.shop_id, s.name_short, COUNT(o.id) as cnt
    FROM tbl_order as o
    JOIN tbl_order_delivery od ON o.id = od.id
    JOIN caru.tbl_suppliers as s ON s.catpro_p::bigint = o.shop_id
    WHERE o.status = 14
    AND o.shop_id > 100
    AND od.type_id = 3
    GROUP BY o.shop_id, s.name_short
    UNION
    SELECT 1 as shop_id, (SELECT name_short FROM caru.tbl_suppliers WHERE id = 1) as name_short, COUNT(o.id) as cnt
    FROM tbl_order as o
    JOIN tbl_order_delivery od ON o.id = od.id
    WHERE o.status = 14
    AND o.shop_id < 100
    AND od.type_id = 3
) as tbl
ORDER BY tbl.shop_id ASC

-- Найти товары с нужным флагом
select p.catpro_id, p.id
from bnd_prod_flags as bpf
join tbl_products_stock as ps on ps.id = bpf.prod_id
join tbl_products as p on p.id = ps.id
where bpf.flag_id = 5
and ps.cnt > 0

-- очистить карантин
delete from bnd_customer_quarantine
where customer_id in (
  select DISTINCT q.customer_id
  from tbl_order as o
  left join bnd_customer_quarantine as q on o.customer_id = q.customer_id
  where o.id IN(13481578,13487270,13487119,13487108,13486432)
)

-- получить определённые поля из KPI
SELECT k.id, k.user_id, k.date_month,
k.data_k::hstore -> 'K8_original' as K8_original,
k.data_k::hstore -> 'K8' as K8,
k.penalty_by_hand, k.bonus_by_hand, k.is_blocked
FROM callcenter.tbl_kpi_calculation as k
WHERE k.date_month = '01.10.2015' and k.user_id IN(2346, 3439, 2125)

-- подготовить контрагентов для автоформирования накладных
update tbl_delivery_city set time_make_bills = '{10,11,12,13,14,15,16,17,18}' where catpro_id like '%CDEK%';
update tbl_delivery_city set time_make_bills = '{10,11,12,13,14,15,16,17,18}' where catpro_id ilike '%boxberry%';
update tbl_delivery_city set time_make_bills = '{10,11,12,13,14,15,16,17,18}' where catpro_id ilike '%iml%';
update tbl_delivery_city set time_make_bills = '{10,11,12,13,14,15,16,17,18}' where catpro_id ilike '%hermes%';
update tbl_delivery_city set time_make_bills = '{10,11,12,13,14,15,16,17,18}' where delivery_id = 1;



















rollback;