/**
 * 节点定义界面
 */

$(function() {

	$('#list').datagrid({

		title: 'topo定义列表',
		width: 700,
		height: 500,
		fitColumns: true,
		striped: true,
		singleSelect: true,
		url: '../ajax.php?c=TopoDefCtrl&a=getAllTopoDef',
		columns: [
			[{
				field: 'topo_type',
				title: '拓扑类型',
				width: 50
			}, {
				field: 'topo_level',
				title: '拓扑级别',
				width: 50
			}, {
				field: 'descr',
				title: '拓扑描述',
				width: 50
			}, {
				field: 'nodeicontype',
				title: '节点图标',
				width: 50
			}, {
				field: 'linklinetype',
				title: 'linklinetype',
				width: 50
			}, {
				field: 'nodeconvtype',
				title: 'nodeconvtype',
				width: 50,
				align: 'right'
			}, {
				field: 'linkconvtype',
				title: 'linkconvtype',
				width: 50
			}]
		]

		/*onClickRow: function(index, row){
			
		}*/

	});

	$('#panel').panel({

		width: 500,
		height: 150,
		title: 'My Panel',

	});

	$('#china').combobox({

		required: true,
		multiple: true,
		//url: 

	});

	$('#province').combobox({

		required: true,
		multiple: true

	});

	$('#city').combobox({

		required: true,
		multiple: true

	});

})