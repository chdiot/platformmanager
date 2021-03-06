<script>
    $(document).ready(function () {

        $("#hider").hide();
        $("#notebuttonclose").click(function () {
            $("#hider").hide();
            $('#notepopup_box').hide();
        });
        $("#taskbuttonclose").click(function () {
            $("#hider").hide();
            $('#taskpopup_box').hide();
        });
        $("#eventbuttonclose").click(function () {
            $("#hider").hide();
            $('#eventpopup_box').hide();
        });

<?php for ($i = 1; $i <= $lastDayIdx; $i++) {
    ?>
            $("#addnote_<?php echo $year ?>_<?php echo $month ?>_<?php echo $i ?>").click(function () {
                //alert("add note clicked");
                var strid = this.id;
                var arrayid = strid.split("_");
                showAddNoteForm(arrayid[1], arrayid[2], arrayid[3], 0);
            });

    <?php
}
?>
        $("#addnote_<?php echo $year ?>_<?php echo $month ?>").click(function () {
            //alert("add note clicked");
            var strid = this.id;
            var arrayid = strid.split("_");
            showAddNoteForm(arrayid[1], arrayid[2], 1, 1);
        });

<?php for ($i = 1; $i <= $lastDayIdx; $i++) {
    ?>
            $("#addtask_<?php echo $year ?>_<?php echo $month ?>_<?php echo $i ?>").click(function () {
                //alert("add note clicked");
                var strid = this.id;
                var arrayid = strid.split("_");
                showAddTaskForm(arrayid[1], arrayid[2], arrayid[3], 0);
            });
    <?php
}
?>
        $("#addtask_<?php echo $year ?>_<?php echo $month ?>").click(function () {
            //alert("add note clicked");
            var strid = this.id;
            var arrayid = strid.split("_");
            showAddTaskForm(arrayid[1], arrayid[2], 1, 1);
        });

<?php for ($i = 1; $i <= $lastDayIdx; $i++) {
    ?>
            $("#addevent_<?php echo $year ?>_<?php echo $month ?>_<?php echo $i ?>").click(function () {
                //alert("add note clicked");
                var strid = this.id;
                var arrayid = strid.split("_");
                showAddEventForm(arrayid[1], arrayid[2], arrayid[3], 0);
            });

    <?php
}
?>
        $("#addevent_<?php echo $year ?>_<?php echo $month ?>").click(function () {
            //alert("add note clicked");
            var strid = this.id;
            var arrayid = strid.split("_");
            showAddEventForm(arrayid[1], arrayid[2], 1, 1);
        });
<?php
for ($i = 0; $i < count($notes); $i++) {
    ?>
            $("#collections_<?php echo $notes[$i]["id"] ?>").click(function () {
                var strid = this.id;
                var arrayid = strid.split("_");
                showNoteCollectionForm(arrayid[1]);
            });
    <?php
    $openlink = "opennote";
    if ($notes[$i]["type"] == 2) {
        $openlink = "opentask";
    }
    if ($notes[$i]["type"] == 3) {
        $openlink = "openevent";
    }
    ?>
            $("#<?php echo $openlink ?>_<?php echo $notes[$i]["id"] ?>").click(function () {
                //alert("edit note clicked");
                var strid = this.id;
                var arrayid = strid.split("_");
    <?php
    if ($notes[$i]["type"] == 1) {
        ?>
                    showeditNoteForm(arrayid[1]);
        <?php
    }
    ?>
    <?php
    if ($notes[$i]["type"] == 2) {
        ?>
                    showeditTaskForm(arrayid[1]);
        <?php
    }
    ?>
    <?php
    if ($notes[$i]["type"] == 3) {
        ?>
                    showeditEventForm(arrayid[1]);
        <?php
    }
    ?>

            });

    <?php
    if ($notes[$i]["type"] == 2) {
        ?>

                $("#closetask_<?php echo $notes[$i]["id"] ?>").click(function () {
                    //alert("edit note clicked");
                    var strid = this.id;
                    var arrayid = strid.split("_");
                    closeTask(arrayid[1]);

                });

                $("#canceltask_<?php echo $notes[$i]["id"] ?>").click(function () {
                    //alert("edit note clicked");
                    var strid = this.id;
                    var arrayid = strid.split("_");
                    cancelTask(arrayid[1]);

                });

        <?php
    }
    ?>
    <?php
}
?>

        function showNoteCollectionForm(id_note) {
            alert("collection not yet implemented ");
            $.post(
                    'bjnotecollections/' + id_note,
                    {},
                    function (data) {

                        $("#hider").fadeIn("slow");
                        $('#collectionspopup_box').fadeIn("slow");
                    },
                    'json'
                    );
        }
        ;

        function showAddNoteForm(year, month, day, is_month) {

            if (day < 10) {
                day = "0" + day;
            }
            var lang = '<?php echo $lang; ?>';
            if (lang === 'fr') {
                $('#formnotedate').val(day + "/" + month + "/" + year);
            } else {
                $('#formnotedate').val(year + "-" + month + "-" + day);
            }
            //alert("day = " + day);
            if (is_month === 1) {
                $('#formnoteismonth').val(1);
            } else {
                $('#formnoteismonth').val(0);
            }
            $('#formnoteid').val(0);
            $('#formnotename').val("");
            $('#formnotecontent').val("");

            $("#hider").fadeIn("slow");
            $('#notepopup_box').fadeIn("slow");
        }
        ;

        function showAddTaskForm(year, month, day, is_month) {

            if (day < 10) {
                day = "0" + day;
            }
            var lang = '<?php echo $lang; ?>';
            if (lang === 'fr') {
                $('#formtaskdate').val(day + "/" + month + "/" + year);
            } else {
                $('#formtaskdate').val(year + "-" + month + "-" + day);
            }
            if (is_month === 1) {
                $('#formtaskismonth').val(1);
            } else {
                $('#formtaskismonth').val(0);
            }
            $('#formtaskid').val(0);
            $('#formtaslname').val("");
            $('#formtaskcontent').val("");
            $('#formtaskdeadline').val("");
            $('#formtaskpriority').val("");

            $("#hider").fadeIn("slow");
            $('#taskpopup_box').fadeIn("slow");
        }
        ;

        function showAddEventForm(year, month, day, is_month) {
            if (day < 10) {
                day = "0" + day;
            }
            var lang = '<?php echo $lang; ?>';
            if (lang === 'fr') {
                $('#formeventdatestart').val(day + "/" + month + "/" + year);
                $('#formeventdateend').val(day + "/" + month + "/" + year);
            } else {
                $('#formeventdatestart').val(year + "-" + month + "-" + day);
                $('#formeventdateend').val(year + "-" + month + "-" + day);
            }
            if (is_month === 1) {
                $('#formeventismonth').val(1);
            } else {
                $('#formeventismonth').val(0);
            }
            $('#formeventdatestartH').val("08");
            $('#formeventdatestartm').val("00");
            $('#formeventdateendH').val("10");
            $('#formeventdateendm').val("00");
            $('#formeventid').val(0);
            $('#formeventname').val("");
            $('#formeventcontent').val("");

            $("#hider").fadeIn("slow");
            $('#eventpopup_box').fadeIn("slow");
        }
        ;

        function showeditNoteForm(id) {
            $.post(
                    'bjgetnote/' + id,
                    {},
                    function (data) {
                        $('#formnotedate').val(data.date);
                        $('#formnoteid').val(data.id);
                        $('#formnotename').val(data.name);
                        $('#formnotecontent').val(data.content);

                        $("#hider").fadeIn("slow");
                        $('#notepopup_box').fadeIn("slow");
                    },
                    'json'
                    );

        }
        ;

        function showeditTaskForm(id) {
            $.post(
                    'bjgettask/' + id,
                    {},
                    function (data) {
                        $('#formtaskdate').val(data.date);
                        $('#formtaskid').val(data.id);
                        $('#formtaskname').val(data.name);
                        $('#formtaskcontent').val(data.content);
                        $('#formtaskdeadline').val(data.deadline);
                        $('#formtaskpriority').val(data.priority);

                        $("#hider").fadeIn("slow");
                        $('#taskpopup_box').fadeIn("slow");
                    },
                    'json'
                    );

        }
        ;

        function showeditEventForm(id) {
            $.post(
                    'bjgetevent/' + id,
                    {},
                    function (data) {
                        $('#formeventdatestart').val(data.startdate);
                        $('#formeventdateend').val(data.enddate);
                        $('#formeventdatestartH').val(data.starthour);
                        $('#formeventdatestartm').val(data.startmin);
                        $('#formeventdateendH').val(data.endhour);
                        $('#formeventdateendm').val(data.endmin);
                        $('#formeventid').val(data.id);
                        $('#formeventname').val(data.name);
                        $('#formeventcontent').val(data.content);

                        $("#hider").fadeIn("slow");
                        $('#eventpopup_box').fadeIn("slow");
                    },
                    'json'
                    );

        }
        ;

        function updateNoteListHtml(data) {
            //alert("start updateNoteListHtml " + JSON.stringify(data));
            var typeicon = "glyphicon glyphicon-minus";
            if (data.type === 2) {
                typeicon = "glyphicon glyphicon-asterisk";
            }
            if (data.type === 3) {
                typeicon = "glyphicon glyphicon-calendar";
            }

            var styleTR = "";
            if (data.type === 2 && data.status === 3) {
                styleTR = "style=\"text-decoration: line-through;\"";
            }
            var htmldata = "<tr id=\"tableline_" + data.id + "\" " + styleTR + ">";
            //alert("pass 1 updateNoteListHtml ");
            var priorityVal = "";
            var cssStatus = "";
            if (data.type === 2) {
                priorityVal = data.priority;
                cssStatus = "background-color:#FF8800;";
                if (data.status === 2 || data.status === 3) {
                    cssStatus = "background-color:#008000;";
                }
            }
            //alert("pass 2 updateNoteListHtml ");
            if (data.type === 2) {
                htmldata += "<td id=\"task_status_" + data.id + "\" style=\"" + cssStatus + "\"><span></span></td>";
            } else {
                htmldata += "<td><span></span></td>";
            }
            //alert("pass 3 updateNoteListHtml ");
            htmldata += "<td>" + priorityVal + "</td>";
            htmldata += "<td><span class=\"" + typeicon + "\"></span></td>";
            var openlink = "opennote";
            if (data.type === 2) {
                openlink = "opentask";
            } else if (data.type === 3) {
                openlink = "openevent";
            }
            //alert("pass 4 updateNoteListHtml ");
            htmldata += "<td><a style=\"color:#666; cursor:pointer;\" id=\"" + openlink + "_" + data.id + "\">" + data.name + "</a></td>";
            //alert("pass 5 updateNoteListHtml ");            
            if (data.type === 2) {
                var editTxt = "<?php echo BulletjournalTranslator::MarkAsDone($lang); ?>";
                if (data.status === 2) {
                    editTxt = "<?php echo BulletjournalTranslator::ReOpen($lang); ?>";
                }
                var cancelTxt = "<?php echo BulletjournalTranslator::Cancel($lang); ?>";
                if (data.status === 3) {
                    cancelTxt = "<?php echo BulletjournalTranslator::ReOpen($lang); ?>";
                }
                htmldata += "<td><button id=\"closetask_" + data.id + "\" class=\"btn btn-xs btn-primary\">" + editTxt + "</button></td>";
                htmldata += "<td><button id=\"canceltask_" + data.id + "\" class=\"btn btn-xs btn-default\">" + cancelTxt + "</button></td>";

            } else {
                htmldata += "<td></td>";
                htmldata += "<td></td>";
            }
            htmldata += "</tr>";
            //alert("pass 6 updateNoteListHtml ");
            //alert("is month = " + data.ismonth);
            var tableID = "#list_" + data.year + "-" + data.month + "-" + data.day;
            if (data.ismonth == 1) {
                tableID = "#list_" + data.year + "-" + data.month;
            }
            $(tableID).append(htmldata);

        }

        $('#editNoteFormsubmit').click(function (e) {
            e.preventDefault();
            $.post(
                    'bjeditnotequery/<?php echo $id_space ?>',
                    $('#editNoteForm').serialize(),
                    function (data) {
                        if (data.isedit === 0) {
                            updateNoteListHtml(data);
                        }
                        $("#hider").hide();
                        $('#notepopup_box').hide();
                    },
                    'json'
                    );

        });

        $('#editTaskFormsubmit').click(function (e) {
            e.preventDefault();
            $.post(
                    'bjedittask/<?php echo $id_space ?>',
                    $('#editTaskForm').serialize(),
                    function (data) {
                        if (data.isedit === 0) {
                            updateNoteListHtml(data);
                        }
                        $("#hider").hide();
                        $('#taskpopup_box').hide();
                    },
                    'json'
                    );

        });

        $('#editEventFormsubmit').click(function (e) {
            e.preventDefault();
            $.post(
                    'bjeditevent/<?php echo $id_space ?>',
                    $('#editEventForm').serialize(),
                    function (data) {
                        if (data.isedit === 0) {
                            updateNoteListHtml(data);
                        }
                        $("#hider").hide();
                        $('#eventpopup_box').hide();
                    },
                    'json'
                    );
        });

        function closeTask(id) {
            //alert("close task clicked " + id);
            $.post(
                    'bjclosetask/' + id,
                    {},
                    function (data) {
                        //alert("update task to " + data.status);
                        if (data.status === 1) {
                            var baliseID = '#task_status_' + id;
                            $(baliseID).css("background-color", "#FF8800");
                            $('#closetask_' + id).text("<?php echo BulletjournalTranslator::MarkAsDone($lang) ?>");
                        } else if (data.status === 2) {
                            $("#task_status_" + id).css("background-color", "#008000");
                            $('#closetask_' + id).text("<?php echo BulletjournalTranslator::ReOpen($lang) ?>");
                        }
                    },
                    'json'
                    );
        }
        ;

        function cancelTask(id) {
            //alert("close task clicked " + id);
            $.post(
                    'bjcanceltask/' + id,
                    {},
                    function (data) {
                        //alert("update task to " + data.status);
                        if (data.status === 1) {
                            var baliseID = '#task_status_' + id;
                            $(baliseID).css("background-color", "#FF8800");
                            $('#canceltask_' + id).text("<?php echo BulletjournalTranslator::Cancel($lang) ?>");
                            $('#tableline_' + id).css("text-decoration", "none");
                        } else if (data.status === 3) {
                            $("#task_status_" + id).css("background-color", "#008000");
                            $('#canceltask_' + id).text("<?php echo BulletjournalTranslator::ReOpen($lang) ?>");
                            $('#tableline_' + id).css("text-decoration", "line-through");
                        }
                    },
                    'json'
                    );
        }
        ;

    });
</script>

