-- выбор уникальных значений в поле hstore
SELECT DISTINCT(tbl.cov)
  FROM (
  SELECT regexp_replace(regexp_replace(cover::hstore -> 'small', 'http://', ''), '/.+$', '') as cov
  FROM tbl_products 
  WHERE cover IS NOT NULL 
  UNION
  SELECT regexp_replace(regexp_replace(cover::hstore -> 'normal', 'http://', ''), '/.+$', '') as cov
  FROM tbl_products 
  WHERE cover IS NOT NULL 
) as tbl 
WHERE tbl.cov IS NOT NULL

-- поиск по hstore
SELECT DISTINCT(prod_id)
FROM tbl_products_media
WHERE path IS NOT NULL AND xtype = 'image'
AND (
	path::hstore -> 'small' NOT LIKE '%cdn1.top-shop.ru%'
	OR path::hstore -> 'normal' NOT LIKE '%cdn1.top-shop.ru%'
	OR path::hstore -> 'big' NOT LIKE '%cdn1.top-shop.ru%'
) 
LIMIT 2

-- обновить значение в hstore
UPDATE tbl_products SET cover = regexp_replace(cover::text, 'c529717.r17.cf3.rackcdn.com', 'cdn1.top-shop.ru', 'g')::hstore;

-- счётчик полученных строк
SELECT row_number() OVER() as id, tbl2.*
FROM (
  SELECT caruindex, caruname, catindex, catname, ord 
  FROM (
    -- получение списка индексов, где отличаются названия
    SELECT pli.index as caruindex, pli.name as caruname, csi.id as catindex, csi.naziv as catname, 1 as ord
    FROM post_lst_index as pli 
    JOIN cat.s_indeks as csi ON pli.index = to_int(csi.id)
    WHERE pli.name <> csi.naziv   
    UNION  
    -- получить список индексов, которых нет в s_index
    SELECT pli.index as caruindex, pli.name as caruname, csi.id as catindex, csi.naziv as catname, 2 as ord
    FROM post_lst_index as pli 
    LEFT JOIN cat.s_indeks as csi ON pli.index = to_int(csi.id)
    WHERE csi.id IS NULL  
    UNION  
    -- получить список индексов, которых нет в post_lst_index
    SELECT pli.index as caruindex, pli.name as caruname, csi.id as catindex, csi.naziv as catname, 3 as ord
    FROM post_lst_index as pli 
    RIGHT JOIN cat.s_indeks as csi ON pli.index = to_int(csi.id)
    WHERE pli.index IS NULL
  ) as tbl
  ORDER BY ord ASC
) as tbl2

-- update from select
update tbl_order 
set status = 14 
from (
  select id from tbl_order where invoice_id is not null and status = 15 
  order by date_create asc limit 500
) as tbl
where tbl_order.id = tbl.id

-- форматирование и приведение типов
select 
CASE 
WHEN (size_pack[1] <> 0 AND size_pack[2] <> 0 AND size_pack[3] <> 0)
THEN to_char((size_pack[1]::bigint * size_pack[2]::bigint * size_pack[3]::bigint)::real / 1000000000, 'FM999990.999999')
ELSE ''
END  as volume
from tbl_products where size_pack is not null

-- выводить по формуле HH:MM:SS как HH.(MM/60)
SELECT (to_char(tbl.amount, 'HH24') 
|| substring(to_char(to_char(tbl.amount, 'MI')::float / 60, '0.99') from 3)
)::float as amount, tbl.id as id
FROM ()

-- Условие в условии
SELECT tsc.address, lstt.name as type_name, lsb.name as block_name, tpc.id as prod_id, 
p.catpro_id, p.name as product_name, tpc.cnt
FROM caru.tbl_store_cells as tsc
JOIN caru.lst_wms_store_types as lstt ON tsc.type_id = lstt.id
JOIN caru.lst_store_blocks as lsb ON tsc.block_id = lsb.id
JOIN caru.tbl_prod_cell as tpc ON tsc.id = tpc.cell_id
JOIN tbl_products as p ON tpc.product_id = p.id
WHERE 
CASE WHEN 'checked' = 'checked' THEN
	 p.id IN (
		SELECT DISTINCT tpc.product_id
		FROM caru.tbl_store_cells as tsc
		JOIN caru.tbl_prod_cell as tpc ON tsc.id = tpc.cell_id
		WHERE tsc.type_id IN (2,3,8,4)
	)
	AND tsc.type_id != 0
ELSE
	tsc.type_id != 0
END

-- Подсчёт суммы по условию
SELECT u.id as user_bo_id, sum(CASE WHEN st.type_id = 3 THEN 1 ELSE 0 END) as task_type_3

-- Диапазон дат по порядку
SELECT '24.03.2014'::DATE + sequence.day AS dates FROM generate_series(0, 5) AS sequence(day) ORDER BY sequence.day

-- Добавить поле hstore в пустое поле
UPDATE lst_order_substatus
SET additional = ('rank' => to_char(tbl.rank, '9'))::hstore
FROM (
	select id, rank from lst_order_substatus_rank
) as tbl
WHERE tbl.id = lst_order_substatus.id

-- объединить вывод с подзапросом
s.name_short || CASE WHEN (SELECT 1 FROM caru.tbl_wb_goods as wbg JOIN tbl_products as p ON p.id = wbg.product_id WHERE wbg.waybill_id = w.id AND p.genre_id = 6058) = 1 THEN ' +ПУ' ::text ELSE '' END as supplier_name

-- Получить все поля таблицы по-порядку
SELECT c.column_name as field, pgd.description as name
FROM pg_catalog.pg_statio_all_tables as st
JOIN pg_catalog.pg_description pgd ON pgd.objoid = st.relid
JOIN information_schema.columns c ON (
  pgd.objsubid = c.ordinal_position
  AND c.table_schema = st.schemaname
  AND c.table_name = st.relname
)
WHERE c.table_name = 'tbl_director_book_bo' AND c.table_schema = 'caru'

-- Вставить данные из другой таблицы
INSERT INTO ext.lst_kladr_kazakhstan SELECT * FROM ONLY caru.lst_kladr_kazakhstan;

-- Сравнить время с секундами и даты
o.date_status > to_timestamp(to_char(NOW() - INTERVAL '1 day', 'DD.MM.YY'), 'DD.MM.YY')

-- вставка дампа
COPY tbl_delivery_tariff (id, delivery_type, weight_from, weight_to, tariff_type) FROM stdin;
184	1	0	6000	1
185	1	6001	12000	1
\.

-- запрос, выдающий список пустых таблиц без единой записи:
SELECT
   schemaname,
   relname
FROM pg_stat_all_tables
WHERE schemaname IN ('public', 'caru') and n_tup_ins = 0
order by schemaname, relname

-- Дата прошлый месяц
SELECT SUM(aor.premium - round(aor.premium * (COALESCE(ar.discount, 0)::float/100)))
FROM caru.tbl_agent_order_report as aor
JOIN caru.tbl_agent_report as ar ON ar.id = aor.report_id
LEFT JOIN tbl_order as o ON o.id = aor.order_id
AND ar.date_report BETWEEN date_trunc('Month',Now()) - interval '1 month' AND date_trunc('Month',Now())
AND ar.supplier_id = 543
AND o.shop_id < 100

-- Группировка. Если одинаковые поля, то складываем вместе.
-- Если есть особенный товар, то отдельной записью в выдаче
-- Если есть флаг у поставщика, то тоже отдельной записью
SELECT row_number() OVER() as id, 
tbl.name_short, tbl.supplier_id, tbl.shop_id, tbl.consignee_string, tbl.delivery_date, COUNT(*) as cnt,
'' as number_order, tbl.type_expiration, tbl.lifetime as order_id, tbl.wms_do_not_ship_wo_pallets_10
FROM (
    SELECT o.id, s.name_short as name_short, s.id as supplier_id, o.shop_id,
    COALESCE(od.additional::hstore->'consignee_string', '') as consignee_string, 
    to_char(od.delivery_date, 'DD.MM.YYYY') as delivery_date,
    (
      SELECT o.id
      FROM tbl_order_item as oi 
      JOIN bnd_prod_flags as bpf ON oi.prod_id = bpf.prod_id
      WHERE oi.order_id = o.id
      AND bpf.flag_id = 15
      LIMIT 1
    ) as lifetime,
    od.additional::hstore->'expiration_priority' as type_expiration,
    CASE 
      WHEN s.options::hstore->'wms_do_not_ship_wo_pallets_10' IN ('2','3')
      THEN o.id
      ELSE 0
    END as wms_do_not_ship_wo_pallets_10
    FROM tbl_order as o
    JOIN tbl_order_delivery as od ON o.id = od.id
    JOIN caru.tbl_suppliers s ON o.shop_id::text = s.catpro_p
    WHERE od.type_id = 0 AND o.shop_id > 100
    AND o.status = 14
) as tbl
GROUP BY tbl.name_short, tbl.supplier_id, tbl.shop_id, tbl.consignee_string, 
tbl.delivery_date, tbl.lifetime, tbl.type_expiration, tbl.wms_do_not_ship_wo_pallets_10

