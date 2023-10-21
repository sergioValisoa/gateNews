$(document).ready(function () {

    var dataTable = $('#post').DataTable(
        {
            rowReorder: {
              selector: 'td:nth-child(2)'
            },
            responsive: true,
            "aaSorting": [],
            "bProcessing": true,
            "bFilter": true,
            "bServerSide": true,
            "iDisplayLength": 10,
            "aLengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "all"]
            ],
            "ajax": {
                url: url_reponse_ajax,
                data: function (data) {
                    if (data.order[0])
                        data.order_by = data.columns[data.order[0].column].name + ' ' + data.order[0].dir;
                }
            },
            "columnDefs": [
                {name: "pst.id", targets: 0},
                {name: "pst.postTitle", targets: 1},
                {name: "pst.postContent",targets: 2},
                {name: "ctg.categoryTitle",targets: 3},
                {name: "pst.postPhotos",
                  render: function (data) {
                    return '<img src="'+data+'" alt="image">'
                  },
                  targets: 4
                },
                {name: "usr.userFullname",targets: 5},
                {name: "pst.postCreatedAt",targets: 6},
                {name: "pst.postUrl", targets: 7},
                {
                    name: "pst.idPost",
                    render: function (data, type, row) {
                      console.log(row);
                        var href_edit = data ? href_edit_default : "javascript:void(0)";
                        var href_delete = data ? href_delete_default : "javascript:void(0)";
                        var href_apercu = data ? href_apercu_default : "javascript:void(0)";
                        href_edit = href_edit.replace('0', data);
                        href_delete = href_delete.replace('0', data);
                        href_apercu = href_apercu.replace('url_default', row[7]);
                      var date = row[6].split('/');
                      var month = date[1];
                      var day = date[0];
                      var year_array = date[2].split(" ");
                      var year = year_array[0];
                        href_apercu = href_apercu.replace('year', year);
                        href_apercu = href_apercu.replace('month', month);
                        href_apercu = href_apercu.replace('day', day);
                        return '<td><a class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Modifier"' +
                            '   href="' + href_edit + '"' +
                            '   data-position="top" data-delay="50"' +
                            '   title="Modifier" data-tooltip="Modifier">' +
                            '    <i class="fa fa-edit"></i>' +
                            '</a> ' +
                            '<a class="btn btn-danger kl-remove-elt" data-toggle="tooltip" data-placement="top" title="Supprimer"' +
                            '   href="' + href_delete + '"' +
                            '   data-position="top" data-delay="50"' +
                            '   title="Supprimer" data-tooltip="Supprimer">' +
                            '    <i class="fa fa-trash"></i>' +
                            '</a> <a class="btn btn-secondary kl-apercu-elt" data-toggle="tooltip" data-placement="top" title="Aperçu"' +
                          '   href="' + href_apercu + '"' +
                          '   data-position="top" data-delay="50" target="_blank" '  +
                          '   title="Aperçu" data-tooltip="Aperçu">' +
                          '    <i class="fa fa-eye"></i>' +
                          '</a></td>';
                    },
                    orderable: false,
                    targets: 8
                },
            ],
            "oLanguage": {
                "sProcessing": "<i class='fa fa-spinner fa-pulse fa-fw kl-spin-ajax'></i>",
                "oPaginate": {
                    "sPrevious": 'Précédent',
                    "sNext": 'Suivant',
                },
                "sSearch": "",
                "sLengthMenu": len_menu,
                "sEmptyTable": 'Aucun enregistrement trouvé',
                "sInfo": info,
                "sZeroRecords": 'Aucun enregistrement trouvé'
            },
            "drawCallback": function (settings) {
                if (settings.aiDisplay.length == 0)
                    $(".paginate_button.next").addClass('disabled');
                $('[data-toggle="tooltip"]').tooltip();

                var api = this.api();
                var num_rows = api.page.info().recordsTotal;
                $('.kl-title-count').html(num_rows);
            }
        }
    );
    $('.kl-search-input').keyup(function () {
        dataTable.search($(this).val()).draw();
    });
});
