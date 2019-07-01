CREATE VIEW seguimientoseco.view_encargos_remedy AS select e.objeto_encargo_id AS objeto_encargo_id,
                                                           e.titulo AS titulo,
                                                           e.numero AS numeroEncargo,
                                                           e.descripcion AS encargoDs,
                                                           e.tiempo_resolucion AS tiempo_resolucion,
                                                           e.horas_realizadas AS horas_realizadas,
                                                           e.solucion_usuario AS solucion_usuario,
                                                           e.solucion_tecnica AS solucion_tecnica,
                                                           e.motivo_cancelacion AS motivo_cancelacion,
                                                           e.criticidad AS criticidad,
                                                           r.numero AS numeroRemedy,
                                                           r.estado AS estado,
                                                           r.centro_id AS centro_id,
                                                           c.descripcion AS centroDs,
                                                           o.descripcion AS operacional1,
                                                           o1.descripcion AS operacional2,
                                                           o2.descripcion AS operacional3,
                                                           a.descripcion AS aplicacion,
                                                           e.fc_registro as fcRegistro,
                                                           e.fc_estado_actual as fcEstadoActual,
                                                           e.fc_entrega as fcEntrega,
                                                           e.fc_cierre as fcCierre,
                                                           r.descripcion_problema AS descripcionProblema
                                                    from seguimientoseco.encargo e
                                                           inner join seguimientoseco.remedy r on e.remedy_id = r.id
                                                           right join seguimientoseco.operacional o on e.operacional1_id = o.id
                                                           right join seguimientoseco.operacional o1 on e.operacional2_id = o1.id
                                                           right join seguimientoseco.operacional o2 on e.operacional3_id = o2.id
                                                           inner join seguimientoseco.aplicacion a on e.aplicacion_id = a.id
                                                           inner join seguimientoseco.centro c on r.centro_id = c.id;
