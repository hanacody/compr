<?php
###################################################################
## 게시판 관련 테이블
###################################################################
$_BOARD_TABLES = array(
// 게시물 테이블(개별)
"rankup_board_scheme" => "
CREATE table `rankup_board_{:board_id:}` (

no : 게시물 번호(ano)
dno : 게시물 분할 번호
cno : 분류 번호

sno : 게시물 순번(-1.....-200000000)
gno : 게시물 묶음 번호(0......n)
pno : 게시물 위치 번호(0......n)

pano : 이전 게시물 번호(최근 글)
nano : 다음 게시물 번호(오래된 글)

uip : 작성자 IP
uid : 작성자 아이디
unick : 작성자 닉네임
upass : 게시물 비밀번호

subject : 제목
content : 내용
attach : 첨부파일(serialize : 원본파일명, 저장파일명, 파일사이즈)
voter : 추천자(find_in_set($id, voter) 으로 검출)

wdate : 작성일시
mdate : 수정일시

sval : 비밀글 여부
nval : 공지글 여부
dval : 삭제 여부

cnum : 댓글 수
dnum : 다운로드 수 - 미사용
gnum : 추천 수 - 기존필드명 : vnum - 2008.12.30 변경
bnum : 반대 수 - 2008.12.30 추가
hnum : 조회 수

",

// 댓글 테이블(개별)
"rankup_board_comment_scheme" => "
CREATE table `rankup_board_comment_{:board_id:}` (

no : 댓글 번호(cno)
ano : 게시물 번호

uip : 댓글 작성자 아이피
uid : 댓글 작성자 아이디
unick : 댓글 작성자 닉네임
upasswd : 댓글 비밀번호

icon : 아이콘
content : 댓글 내용
wdate : 작성일시

");


###################################################################
## 기본 설정 테이블
###################################################################
$_BOARD_CONFIG_TABLES = array(
// 게시판 환경설정 테이블(통합)
"rankup_board_config" => "

no : 인덱스(bno)
id : 게시판 아이디
name : 게시판 이름
skin : 스킨 폴더 이름
style : 게시판 스타일 - 게시판 or 갤러리 or 웹진 or 1:1 형
rank : 순위

cno : 카테고리 번호
pcno : 상위 카테고리 번호
anum : 게시물 수

uval : 게시판 사용여부
mval : 메인페이지에 노출여부
pcmval : 상위카테고리 메인페이지에 노출여부
smlayout : 메인페이지 레이아웃 설정(출력형태, 제목글자제한, 게시물수/페이지, 이미지가로/세로사이즈)
spcmlayout : 메인페이지 레이아웃 설정(출력형태, 제목글자제한, 게시물수/페이지, 이미지가로/세로사이즈)

slayout : 게시판 레이아웃 설정(serialize : 가로크기, 제목글자제한, 게시물수/페이지)
sfunction : 기능 설정(serialize : 분류기능, 댓글기능, 답글기능, 추천기능, 신고기능)
soption :  선택사항 설정(serialize : 비밀글, 최근게시물, 목록이미지, 답글아이콘, 내용보기내 리스트, 이전/다음글)
scategory : 분류(serialize : 분류번호, 분류명, 등록된 게시물 수)
spermission : 권한(serialize : 목록보기, 내용보기, 글쓰기, 댓글쓰기, 답글쓰기, 삭제하기, 공지글쓰기, 비밀글보기)
spoint : 포인트(serialize : 글쓰기, 내용보기, 댓글쓰기, 답글쓰기, 추천하기, 파일첨부, 파일받기 - 각각 사용여부와 포인트)
sattach : 첨부파일(serialize : 기능사용, 상세페이지 download 정보, 갯수, 허용확장자, 최대용량)
sgallery : 갤러리설정(serialize : 리스트(이미지가로/세로사이즈, 출력갯수/줄), 상세페이지(이미지 가로사이즈))

scontent : 기본 텍스트
sfilter : 단어 필터
sblock : 아이피블럭

",

// 카테고리 테이블
"rankup_board_category" => "

no
pno : 상위 카테고리 번호
mbno : 메인페이지를 사용하지 안을때 노출될 게시판 번호 - '메인게시판관리'에서 설정함

content : 카테고리 이름
rank : 순위
mskin : 메인페이지 스킨
mval : 메인페이지 사용여부
mbnum : 한줄에 출력할 게시판수
sprint : 출력설정(serialize : 이번주, 조회수, 댓글수 베스트, 새로등록된 글)
bval : 게시판 존재 여부
cval : 하위 카테고리 존재 여부
uval : 사용여부
pval : 노출여부
dval : 삭제여부

",

// 게시물 분할 테이블(통합)
"rankup_board_division" => "

no : 인덱스(dno)
bid : 게시판 아이디
division : 분할 번호
banum : 게시판 게시물 수

",

// 조회수 베스트 테이블 - 수정/삭제시 함께 처리
"rankup_board_hit_best" => "
no
pcno : 게시판 상위메뉴
bid : 게시판 아이디
dno : 분할 번호
ano : 게시물 번호
asubject : 게시물 제목
ahnum : 게시물 조회수

",

// 댓글수 베스트 테이블 - 게시물 삭제시 함께 처리
"rankup_board_comment_best" => "
no
pcno : 게시판 상위메뉴
bid : 게시판 아이디
dno : 분할 번호
ano : 게시물 번호
asubject : 게시물 제목
acnum : 게시물 조회수

",

);

?>