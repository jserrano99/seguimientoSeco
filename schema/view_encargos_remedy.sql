CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `view_encargos_remedy` AS
    SELECT 
        `e`.`objeto_encargo_id` AS `objeto_encargo_id`,
        `e`.`titulo` AS `titulo`,
        `e`.`numero` AS `numeroEncargo`,
        `e`.`descripcion` AS `encargoDs`,
        `e`.`tiempo_resolucion` AS `tiempo_resolucion`,
        `e`.`horas_realizadas` AS `horas_realizadas`,
        `e`.`solucion_usuario` AS `solucion_usuario`,
        `e`.`solucion_tecnica` AS `solucion_tecnica`,
        `e`.`motivo_cancelacion` AS `motivo_cancelacion`,
        `e`.`criticidad` AS `criticidad`,
        `r`.`numero` AS `numeroRemedy`,
        `r`.`estado` AS `estado`,
        `r`.`centro_id` AS `centro_id`,
        `c`.`descripcion` AS `centroDs`,
        `a`.`descripcion` AS `aplicacion`,
        `e`.`fc_registro` AS `fcRegistro`,
        `e`.`fc_estado_actual` AS `fcEstadoActual`,
        `e`.`fc_entrega` AS `fcEntrega`,
        `e`.`fc_cierre` AS `fcCierre`,
        `r`.`fecha_modificacion` AS `fcEstadoRemedy`,
        `r`.`descripcion_problema` AS `descripcionProblema`,
        `m`.`id` AS `mesId`,
        `m`.`descripcion` AS `mesDs`
    FROM
        (((((`remedy` `r`
        JOIN `encargo_remedy` `er` ON ((`er`.`remedy_id` = `r`.`id`)))
        JOIN `encargo` `e` ON ((`e`.`id` = `er`.`encargo_id`)))
        JOIN `aplicacion` `a` ON ((`e`.`aplicacion_id` = `a`.`id`)))
        JOIN `centro` `c` ON ((`r`.`centro_id` = `c`.`id`)))
        JOIN `meses` `m` ON ((`r`.`fecha_modificacion` BETWEEN `m`.`fecha_inicio` AND `m`.`fecha_fin`)))